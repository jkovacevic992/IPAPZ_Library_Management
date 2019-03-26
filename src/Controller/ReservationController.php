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

    public function reservations(ReservationRepository $reservationRepository)
    {
        $reservations = $reservationRepository->findAll();
    }
}