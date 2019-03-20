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
//
//    public static function paypalApi()
//    {
//        $api = new ApiContext(
//            new OAuthTokenCredential(
//                'AZP_hxOHbXw1VmplKx7E99xQKF6tyd2L6NTOHHpOOVUAvovo3XpOdzgd4EABOSJLaf6-iJw9XB5M2bEt',
//                'ENRfCoA3NIKRcVzevi8xqFSza7LV2j6HKIIroDBmBkVIXVQn5QMfr3VwS9W9n90V209gF-pNnYR5bF74'
//            )
//        );
//
//        $api->setConfig([
//            'mode' => 'sandbox',
//            'http.ConnectionTimeOut' => 30,
//            'log.LogEnabled' => false,
//            'log.File' => '',
//            'log.LogLevel' => 'FINE',
//            'validation.level' => 'log'
//        ]);
//
//        return $api;
//    }
//    /**
//     * @Route("/profile/paypal", name="paypal")
//     */
//    public function payment(EntityManagerInterface $entityManager, UserInterface $user)
//    {
//
//
//        $payer = new Payer();
//
//        $amount = new Amount();
//        $transaction = new Transaction();
//        $payment = new Payment();
//        $redirectUrls = new RedirectUrls();
//
//        $payer->setPaymentMethod('paypal');
//
//        $amount->setCurrency('USD')
//            ->setTotal('5.00');
//
//
//        $transaction->setAmount($amount)
//            ->setDescription('Membership');
//
//        $payment->setIntent('sale')
//            ->setPayer($payer)
//            ->setTransactions([$transaction]);
//
//        $redirectUrls->setReturnUrl('http://zavrsni.inchoo4u.net/index.php/profile/pay?approved=true')
//            ->setCancelUrl('http://zavrsni.inchoo4u.net/index.php/profile/pay?approved=false');
//
//        $payment->setRedirectUrls($redirectUrls);
//
//        try{
//
//            $payment->create(self::paypalApi());
//
//            $hash = md5($payment->getId());
//            $_SESSION ['paypal_hash'] = $hash;
//
//            $paypalTransaction = new PaypalTransaction();
//            $paypalTransaction->setUser($user);
//            $paypalTransaction->setHash($hash);
//            $paypalTransaction->setComplete(0);
//            $paypalTransaction->setPayment($payment->getId());
//            $paypalTransaction->setAmount($amount->getTotal());
//            $entityManager->persist($paypalTransaction);
//            $entityManager->flush();
//
//
//        }catch(PayPalConnectionException $e){
//            $e->getMessage();
//        }
//
//
//        foreach($payment->getLinks() as $link){
//
//            if($link->getRel() === 'approval_url'){
//                $redirectUrl = $link->getHref();
//            }
//
//
//        }
//        return self::redirect($redirectUrl);
//    }
//
//    /**
//     * @Route("/profile/pay", name="pay")
//     * @param EntityManagerInterface $entityManager
//     * @return Response
//     */
//    public function pay(EntityManagerInterface $entityManager)
//    {
//
//        if(isset($_GET['approved'])){
//            $approved = $_GET['approved'] === 'true';
//
//            if($approved){
//                $payerId = $_GET['PayerID'];
//
//                $paymentId = $entityManager->createQuery('select p.payment from App\Entity\PaypalTransaction p where p.hash=:hash')
//                    ->setParameter('hash', $_SESSION['paypal_hash'])
//                    ->getResult();
//
//
//                $payment = Payment::get($paymentId[0]['payment'], self::paypalApi());
//
//                $execution = new PaymentExecution();
//                $execution->setPayerId($payerId);
//
//                $payment->execute($execution, self::paypalApi());
//
//                $entityManager->createQueryBuilder()
//                    ->update('App\Entity\PaypalTransaction', 'p')
//                    ->set('p.complete',1)
//                    ->where('p.hash = :hash')
//                    ->setParameter('hash', $_SESSION['paypal_hash'])
//                    ->getQuery()
//                    ->getResult();
//                $entityManager->flush();
//                unset($_SESSION['paypal_hash']);
//                return $this->render('paypal/success.html.twig');
//            }else{
//                return  $this->render('paypal/cancelled.html.twig');
//            }
//        }
//    }


public function paypal()
{

    $gateway = new \Braintree_Gateway([
        'environment' => 'sandbox',
        'merchantId' => 'kzftwpnnt5t7gfrf',
        'publicKey' => 'x267p4jntgzy7thj',
        'privateKey' => 'dfc284aeeb9f8c71709bf19987541f88'
    ]);


    $result = $gateway->transaction()->sale([
        'amount' => $_POST['amount'],
        'paymentMethodNonce' => $_POST['payment_method_nonce'],
        'orderId' => $_POST["Mapped to PayPal Invoice Number"],
        'options' => [
            'submitForSettlement' => True,
            'paypal' => [
                'customField' => $_POST["PayPal custom field"],
                'description' => $_POST["Description for PayPal email receipt"],
            ],
        ],
    ]);
    if ($result->success) {
        print_r("Success ID: " . $result->transaction->id);
    } else {
        print_r("Error Message: " . $result->message);
    }
}

    /**
         * @Route("/profile/pay", name="pay")

         * @return Response
         */
    public function paypalShow()
{
    $gateway = new \Braintree_Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'kzftwpnnt5t7gfrf',
    'publicKey' => 'x267p4jntgzy7thj',
    'privateKey' => 'dfc284aeeb9f8c71709bf19987541f88'
]);
    return $this->render('paypal/paypal.html.twig',[
        'gateway' => $gateway
    ]);
}


    /**
     * @Route("/profile/payment", name="payment")
     */
public function payment()
{
    $gateway = new \Braintree_Gateway([
        'environment' => 'sandbox',
        'merchantId' => 'kzftwpnnt5t7gfrf',
        'publicKey' => 'x267p4jntgzy7thj',
        'privateKey' => 'dfc284aeeb9f8c71709bf19987541f88'
    ]);
    $amount = $_POST["amount"];
    $nonce = $_POST["payment_method_nonce"];
    $result = $gateway->transaction()->sale([
        'amount' => $amount,
        'paymentMethodNonce' => $nonce
    ]);
    if ($result->success || !is_null($result->transaction)) {

        header("Location: index.php");
    } else {
        $errorString = "";
        foreach($result->errors->deepAll() as $error) {
            $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
        }
        $_SESSION["errors"] = $errorString;
        header("Location: index.php");
    }

    return $this->render('paypal/text.html.twig',[
        'code' => var_dump($_POST)
        ]);
}

}