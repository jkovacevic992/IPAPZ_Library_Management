<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/18/19
 * Time: 2:30 PM
 */

namespace App\Controller;


use App\Entity\Borrowed;
use App\Entity\PaypalTransaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PaypalTransactionController extends AbstractController
{



    /**
     * @Route("/profile/pay/{id}", name="pay")
     * @return Response
     */
    public function paypalShow(Borrowed $borrowed)
{

    $lateFee= self::calculateLateFeeBorrowed($borrowed);
    $gateway = self::gateway();

    return $this->render('paypal/paypal.html.twig',[
        'gateway' => $gateway,
        'borrowed' => $borrowed,
        'fee' => $lateFee
    ]);
}


    /**
     * @Route("/profile/payment/{id}", name="payment")
     */
public function payment(UserInterface $user, Borrowed $borrowed, EntityManagerInterface $entityManager)
{
    $lateFee= self::calculateLateFeeBorrowed($borrowed);
    $gateway = self::gateway();
    $amount = $lateFee;
    $nonce = $_POST["payment_method_nonce"];
    $result = $gateway->transaction()->sale([
        'amount' => $amount,
        'paymentMethodNonce' => $nonce
    ]);
    $transaction = $result->transaction;

    $paypalTransaction = new PaypalTransaction();
    $paypalTransaction->setAmount($amount);
    $paypalTransaction->setComplete(true);
    $paypalTransaction->setPayment($transaction->id);
    $paypalTransaction->setUser($user);

    $borrowed->setPaymentMethod('PayPal');
    $entityManager->persist($borrowed);

    $entityManager->persist($paypalTransaction);
    $entityManager->flush();



$this->addFlash('success','Payment successful!');
    return $this->redirectToRoute('book_index');
}

public function gateway()
{
    return $gateway = new \Braintree_Gateway([
        'environment' => 'sandbox',
        'merchantId' => 'kzftwpnnt5t7gfrf',
        'publicKey' => 'x267p4jntgzy7thj',
        'privateKey' => 'dfc284aeeb9f8c71709bf19987541f88'
    ]);
}

public function calculateLateFeeBorrowed($borrowed)
{
    $time = $borrowed->getReturnDate();
    $timeDiff = date_diff(new \DateTime('now'), $time)->d;
    $lateFee = sprintf("%.2f",$timeDiff* 0.5 * count($borrowed->getBorrowedBooks()));

    return $lateFee;
}
    /**
     * @Route("/user/pay-premium", name="payPremium")
     */
public function premiumMembership(UserInterface $user, EntityManagerInterface $entityManager)
{
    $gateway = self::gateway();
    $nonce = $_POST["payment_method_nonce"];
    $amount = '10.00';
    $result = $gateway->transaction()->sale([
        'amount' => $amount,
        'paymentMethodNonce' => $nonce
    ]);
    $transaction = $result->transaction;

    $paypalTransaction = new PaypalTransaction();
    $paypalTransaction->setAmount($amount);
    $paypalTransaction->setComplete(true);
    $paypalTransaction->setPayment($transaction->id);
    $paypalTransaction->setUser($user);
    $user->setRoles(['ROLE_PREMIUM_USER']);
    $entityManager->persist($user);
    $entityManager->persist($paypalTransaction);
    $entityManager->flush();

    $this->addFlash('success','You are now a premium member, please login.');
    return $this->redirectToRoute('logout');
}

    /**
     * @Route("/user/premium-membership", name="premiumMembership")
     * @return Response
     */
    public function paypalPremium()
    {


        $gateway = self::gateway();

        return $this->render('paypal/paypal_premium.html.twig',[
            'gateway' => $gateway

        ]);
    }


}