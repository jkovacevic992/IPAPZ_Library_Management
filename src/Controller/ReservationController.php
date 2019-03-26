<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/26/19
 * Time: 11:24 AM
 */

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/reservations", name="reservations")
     * @param ReservationRepository $reservationRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reservations(ReservationRepository $reservationRepository)
    {
        $reservations = $reservationRepository->findBy(['active' => true]);

        return $this->render(
            'reservation/reservations.html.twig',
            [
            'reservations' => $reservations
            ]
        );
    }
}