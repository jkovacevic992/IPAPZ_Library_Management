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
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PaypalTransactionController extends AbstractController
{


    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/pay/{id}", name="pay")
     * @param Borrowed $borrowed
     * @return                     \Symfony\Component\HttpFoundation\Response
     */
    public function paypalDisplay(Borrowed $borrowed)
    {

        $lateFee = $this->calculateLateFeeBorrowed($borrowed);
        $gateway = $this->gateway();

        return $this->render(
            'paypal/paypal.html.twig',
            [
                'gateway' => $gateway,
                'borrowed' => $borrowed,
                'fee' => $lateFee
            ]
        );
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/payment/{id}", name="payment")
     * @param UserInterface $user
     * @param Borrowed $borrowed
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function payment(
        UserInterface $user,
        Borrowed $borrowed,
        EntityManagerInterface $entityManager,
        Request $request
    ) {
        $lateFee = $this->calculateLateFeeBorrowed($borrowed);
        $gateway = $this->gateway();
        $amount = $lateFee;
        $nonce = $request->get('payment_method_nonce');
        $result = $gateway->transaction()->sale(
            [
                'amount' => $amount,
                'paymentMethodNonce' => $nonce
            ]
        );
        $transaction = $result->transaction;

        $paypalTransaction = $this->payPalTransaction($amount, $transaction, $user);

        $borrowed->setPaymentMethod('PayPal');
        $entityManager->persist($borrowed);

        $entityManager->persist($paypalTransaction);
        $entityManager->flush();


        $this->addFlash('success', 'Payment successful!');
        return $this->redirectToRoute('book_index');
    }

    public function gateway()
    {
        return $gateway = new \Braintree_Gateway(
            [
                'environment' => 'sandbox',
                'merchantId' => 'kzftwpnnt5t7gfrf',
                'publicKey' => 'x267p4jntgzy7thj',
                'privateKey' => 'dfc284aeeb9f8c71709bf19987541f88'
            ]
        );
    }

    public function calculateLateFeeBorrowed($borrowed)
    {
        $time = $borrowed->getReturnDate();
        $timeDiff = date_diff(new \DateTime('now'), $time)->d;
        $lateFee = sprintf("%.2f", $timeDiff * 0.5 * count($borrowed->getBorrowedBooks()));

        return $lateFee;
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/user/pay-premium", name="payPremium")
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function premiumMembership(
        Security $security,
        EntityManagerInterface $entityManager,
        Request $request
    ) {
        $gateway = $this->gateway();
        $nonce = $request->get('payment_method_nonce');
        $amount = '10.00';
        $result = $gateway->transaction()->sale(
            [
                'amount' => $amount,
                'paymentMethodNonce' => $nonce
            ]
        );
        $transaction = $result->transaction;
        $user = $security->getUser();
        $paypalTransaction = $this->payPalTransaction($amount, $transaction, $user);
        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setCreatedAt(new \DateTime('now'));
        $user->setMembership(true);
        $entityManager->persist($subscription);
        $entityManager->persist($user);
        $entityManager->persist($paypalTransaction);
        $entityManager->flush();


        $this->addFlash('success', 'You are now a premium member.');
        return $this->redirectToRoute('book_index');
    }

    public function payPalTransaction($amount, $transaction, $user)
    {

        $paypalTransaction = new PaypalTransaction();
        $paypalTransaction->setAmount($amount);
        $paypalTransaction->setComplete(true);

        $paypalTransaction->setPayment($transaction->id);

        $paypalTransaction->setUser($user);

        return $paypalTransaction;
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/user/premium-membership", name="premiumMembership")
     * @return                            \Symfony\Component\HttpFoundation\Response
     */
    public function paypalPremium()
    {


        $gateway = $this->gateway();

        return $this->render(
            'paypal/paypal_premium.html.twig',
            [
                'gateway' => $gateway

            ]
        );
    }
}
