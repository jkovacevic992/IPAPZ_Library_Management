<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/26/19
 * Time: 11:24 AM
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Sodium\add;
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

    /**
     * @Symfony\Component\Routing\Annotation\Route("/user/cancel_reservation/{reservation}/{book}",
     *     name="cancel_reservation")
     * @param Reservation $reservation
     * @param Book $book
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelReservation(Reservation $reservation, Book $book, EntityManagerInterface $em)
    {

        $book->setReservation(null);
        $reservation->setBook(null);
        $reservation->setUser(null);
        $reservation->setActive(false);

        $book->setNotification(false);
        $em->persist($book);
        $em->persist($reservation);
        $em->flush();
        $this->addFlash('success', 'Book removed from reservations!');
        return $this->redirectToRoute('book_index');
    }
}