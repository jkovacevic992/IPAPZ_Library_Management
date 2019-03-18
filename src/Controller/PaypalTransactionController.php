<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/18/19
 * Time: 2:30 PM
 */

namespace App\Controller;


use App\Entity\PaypalTransaction;
use Doctrine\ORM\EntityManagerInterface;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PaypalTransactionController extends AbstractController
{

    public function paypalApi()
    {
        $api = new ApiContext(
            new OAuthTokenCredential(
                'AZP_hxOHbXw1VmplKx7E99xQKF6tyd2L6NTOHHpOOVUAvovo3XpOdzgd4EABOSJLaf6-iJw9XB5M2bEt',
                'ENRfCoA3NIKRcVzevi8xqFSza7LV2j6HKIIroDBmBkVIXVQn5QMfr3VwS9W9n90V209gF-pNnYR5bF74'
            )
        );

        $api->setConfig([
            'mode' => 'sandbox',
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => false,
            'log.File' => '',
            'log.LogLevel' => 'FINE',
            'validation.level' => 'log'
        ]);

        return $api;
    }
    /**
     * @Route("/profile/paypal", name="paypal")
     */
    public function payment(EntityManagerInterface $entityManager, UserInterface $user, $totalAmount)
    {


        $payer = new Payer();

        $amount = new Amount();
        $transaction = new Transaction();
        $payment = new Payment();
        $redirectUrls = new RedirectUrls();

        $payer->setPaymentMethod('paypal');

        $amount->setCurrency('USD')
            ->setTotal($totalAmount);


        $transaction->setAmount($amount)
            ->setDescription('Membership');

        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction]);

        $redirectUrls->setReturnUrl('http://zavrsni.inchoo4u.net/index.php/profile/pay?approved=true')
            ->setCancelUrl('http://zavrsni.inchoo4u.net/index.php/profile/pay?approved=false');

        $payment->setRedirectUrls($redirectUrls);

        try{

            $payment->create($this->paypalApi());

            $hash = md5($payment->getId());
            $_SESSION ['paypal_hash'] = $hash;

            $paypalTransaction = new PaypalTransaction();
            $paypalTransaction->setUser($user);
            $paypalTransaction->setHash($hash);
            $paypalTransaction->setComplete(0);
            $paypalTransaction->setPayment($payment->getId());
            $entityManager->persist($paypalTransaction);
            $entityManager->flush();


        }catch(PayPalConnectionException $e){
            $e->getMessage();
        }


        foreach($payment->getLinks() as $link){

            if($link->getRel() === 'approval_url'){
                $redirectUrl = $link->getHref();
            }


        }
        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/profile/pay", name="pay")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function pay(EntityManagerInterface $entityManager)
    {

        if(isset($_GET['approved'])){
            $approved = $_GET['approved'] === 'true';

            if($approved){
                $payerId = $_GET['PayerID'];

                $paymentId = $entityManager->createQuery('select p.payment from App\Entity\PaypalTransaction p where p.hash=:hash')
                    ->setParameter('hash', $_SESSION['paypal_hash'])
                    ->getResult();


                $payment = Payment::get($paymentId[0]['payment'], $this->paypalApi());

                $execution = new PaymentExecution();
                $execution->setPayerId($payerId);

                $payment->execute($execution, $this->paypalApi());

                $entityManager->createQueryBuilder()
                    ->update('App\Entity\PaypalTransaction', 'p')
                    ->set('p.complete',1)
                    ->where('p.hash = :hash')
                    ->setParameter('hash', $_SESSION['paypal_hash'])
                    ->getQuery()
                    ->getResult();
                $entityManager->flush();
                unset($_SESSION['paypal_hash']);
                return $this->render('paypal/success.html.twig');
            }else{
                return  $this->render('paypal/cancelled.html.twig');
            }
        }
    }

}