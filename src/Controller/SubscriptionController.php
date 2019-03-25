<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/21/19
 * Time: 9:54 AM
 */

namespace App\Controller;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class SubscriptionController
 * @package App\Controller
 */
class SubscriptionController extends AbstractController
{

    /**
     * @Symfony\Component\Routing\Annotation\Route("/subscription", name="subscription")
     * @param SubscriptionRepository $subscriptionRepository
     * @param EntityManagerInterface $entityManager
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkSubscription(
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ) {
        $subscriptions = $subscriptionRepository->findAll();
        $today = new \DateTime('now');

        foreach ($subscriptions as $subscription) {
            $diff = $today->diff($subscription->getCreatedAt())->format("%a");
            if ($diff > 30) {
                $user = $subscription->getUser();
                $user->setRoles([]);
                $user->setMembership(false);
                $subscription->setActive(false);
                $entityManager->persist($user);
                $entityManager->persist($subscription);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('book_index');
    }
}
