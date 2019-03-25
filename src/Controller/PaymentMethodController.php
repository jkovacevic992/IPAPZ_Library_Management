<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/21/19
 * Time: 11:52 AM
 */

namespace App\Controller;

use App\Entity\PaymentMethod;
use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentMethodController extends AbstractController
{

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/disable_payment/{id}", name="disable_payment")
     * @param PaymentMethod $paymentMethod
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function disablePaymentMethod(PaymentMethod $paymentMethod, EntityManagerInterface $entityManager)
    {

        $paymentMethod->setActive(false);
        $entityManager->persist($paymentMethod);
        $entityManager->flush();

        return $this->redirectToRoute('payment_methods');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/enable_payment/{id}", name="enable_payment")
     * @param PaymentMethod $paymentMethod
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enablePaymentMethod(PaymentMethod $paymentMethod, EntityManagerInterface $entityManager)
    {

        $paymentMethod->setActive(true);
        $entityManager->persist($paymentMethod);
        $entityManager->flush();

        return $this->redirectToRoute('payment_methods');
    }



    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/payment_methods", name="payment_methods")
     * @param PaymentMethodRepository $paymentMethodRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayPaymentMethods(PaymentMethodRepository $paymentMethodRepository)
    {
        $paymentMethods = $paymentMethodRepository->findAll();

        return $this->render(
            'payment/payments.html.twig',
            [
            "paymentMethods" => $paymentMethods
            ]
        );
    }
}
