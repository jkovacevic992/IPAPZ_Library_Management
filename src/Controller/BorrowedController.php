<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 22.02.19.
 * Time: 14:40
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrowed;
use App\Entity\BorrowedBooks;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\BorrowedFormType;
use App\Repository\BorrowedRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BorrowedController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/borrowed_books", name="borrowed_books")
     * @param                             BorrowedRepository $borrowedRepository
     * @param PaymentMethodRepository $paymentMethodRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function borrowedBooks(
        BorrowedRepository $borrowedRepository,
        PaymentMethodRepository $paymentMethodRepository,
        Request $request
    ) {
        $paymentMethods = $paymentMethodRepository->findAll();
        $borrowedBooks = $borrowedRepository->findBy(['active' => true]);
        $lateFee = [];
        $daysLate = [];
        $borrowedFor = [];
        $form = $this->createFormBuilder(null)
            ->add(
                'user', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'placeholder' => 'Please select user',
                'label' => false,
                'choice_label' => function ($user) {
                    /**
                     * @var User $user
                     */
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->findUsersWithBooks();
                }
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        $user = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $borrowedBooks = $borrowedRepository->findBy(['user' => $user, 'active' => true]);
        }


        foreach ($borrowedBooks as $item) {
            $time = $item->getReturnDate();
            $timeDiff = date_diff(new \DateTime('now'), $time)->d;
            if (new \DateTime('now') < $item->getBorrowDate()) {
                $borrowedFor[$item->getId()] = 0;
            } else {
                $borrowedFor[$item->getId()] = date_diff(new \DateTime('now'), $item->getBorrowDate())->d;
            }

            if ($time < new \DateTime('now')) {
                $lateFee[$item->getId()] = sprintf("%.2f", $timeDiff * 0.5 * count($item->getBorrowedBooks()));
                $daysLate[$item->getId()] = $timeDiff;
            } else {
                $lateFee[$item->getId()] = sprintf("%.2f", 0);
                $daysLate[$item->getId()] = 0;
            }
        }

        return $this->render(
            'book/borrowed_books.html.twig',
            [

                'borrowed' => $borrowedBooks,
                'lateFee' => $lateFee,
                'daysLate' => $daysLate,
                'borrowedFor' => $borrowedFor,
                'paymentMethods' => $paymentMethods,
                'form' => $form->createView()

            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/lend_book", name="lend_book")
     * @param                        Request $request
     * @param                        EntityManagerInterface $entityManager
     * @return                       \Symfony\Component\HttpFoundation\Response
     */


    public function lendBook(Request $request, EntityManagerInterface $entityManager)
    {
        $borrowed = new Borrowed();
        try {
            $borrowed->setBorrowDate(new \DateTime('now'));
            $borrowed->setReturnDate(new \DateTime('now + 15 day'));
        } catch (\Exception $e) {
            $e->getMessage();
        }

        $form = $this->createForm(BorrowedFormType::class, $borrowed);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /**
             * @var Borrowed $borrowed
             */
            $borrowed = $form->getData();
            $userId = $borrowed->getUser();
            $user = $entityManager->find(User::class, $userId);
            $user->setHasBooks(true);
            /**
             * @var BorrowedBooks $borrowedBook
             */
            foreach ($borrowed->getBorrowedBooks() as $borrowedBook) {
                $borrowedBook->getBook()->setBorrowedQuantity($borrowedBook->getBook()->getBorrowedQuantity() + 1);
                $borrowedBook->getBook()->setQuantity($borrowedBook->getBook()->getQuantity() - 1);
                if ($borrowedBook->getBook()->getQuantity() < 0) {
                    $this->addFlash(
                        'warning',
                        $borrowedBook->getBook()
                        ->getName() . ' is not available in so many copies.'
                    );
                    return $this->redirectToRoute('book_index');
                }

                if ($borrowedBook->getBook()->getQuantity() === 0) {
                    $borrowedBook->getBook()->setAvailable(false);
                }
            }

            $entityManager->persist($borrowed);

            $entityManager->flush();

            $this->addFlash('success', 'Nice!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/lend_book.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/return_books/{id}", name="return_books")
     * @param                                Borrowed $borrowedId
     * @param                                EntityManagerInterface $entityManager
     * @return                               \Symfony\Component\HttpFoundation\Response
     */
    public function returnBooks(Borrowed $borrowedId, EntityManagerInterface $entityManager)
    {
        /**
         * Borrowed $borrowedId
         */


        foreach ($borrowedId->getBorrowedBooks() as $borrowedBook) {
            $borrowedId->removeBorrowedBook($borrowedBook);
            $borrowedBook->getBook()->setQuantity($borrowedBook->getBook()->getQuantity() + 1);
            $borrowedBook->getBook()->setBorrowedQuantity($borrowedBook->getBook()->getBorrowedQuantity() - 1);
            if ($borrowedBook->getBook()->getQuantity() > 0) {
                $borrowedBook->getBook()->setAvailable(true);
            }
        }

        $borrowedId->setActive(false);
        $user = $entityManager->find(User::class, $borrowedId->getUser());
        $temp = true;
        foreach ($user->getBorrowed() as $borrowed) {
            if (count($borrowed->getBorrowedBooks()) !== 0 || $borrowed->getActive() === true) {
                $temp = false;
                break;
            }
        }

        if ($temp) {
            $user->setHasBooks(false);
        }

        $entityManager->persist($borrowedId);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned all books!');
        return $this->redirectToRoute('borrowed_books');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/return_single_book/{id}/{borrowedId}/{borrowedBooks}", name= "return_single_book")
     * @param BorrowedBooks $borrowedBooks
     * @param                                                                   Book $book
     * @param                                                                   Borrowed $borrowedId
     * @param                                                                   EntityManagerInterface $entityManager
     * @return                                                                  \Symfony\Component\HttpFoundation\Response
     */
    public function returnSingleBook(
        BorrowedBooks $borrowedBooks,
        Book $book,
        Borrowed $borrowedId,
        EntityManagerInterface $entityManager
    ) {
        $book->setQuantity($book->getQuantity() + 1);
        $book->setBorrowedQuantity($book->getBorrowedQuantity() - 1);
        $borrowedId->removeBorrowedBook($borrowedBooks);
        if ($book->getQuantity() > 0) {
            $book->setAvailable(true);
        }

        $user = $entityManager->find(User::class, $borrowedId->getUser());

        if (count($borrowedId->getBorrowedBooks()) === 0) {
            $borrowedId->setActive(false);
            $borrowedId->setPaymentMethod('On Delivery');
        }

        $temp = true;
        foreach ($user->getBorrowed() as $borrowed) {
            if (count($borrowed->getBorrowedBooks()) !== 0 || $borrowed->getActive() === true) {
                $temp = false;
                break;
            }
        }

        if ($temp) {
            $user->setHasBooks(false);
        }

        $entityManager->persist($borrowedId);
        $entityManager->persist($book);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned one book!');
        return $this->redirectToRoute('borrowed_books');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/books_details/{id}", name="books_details")
     * @param                                 Borrowed $borrowed
     * @return                                \Symfony\Component\HttpFoundation\Response
     */
    public function booksDetails(Borrowed $borrowed)
    {
        return $this->render(
            'book/books_details.html.twig',
            [
                'borrowed' => $borrowed
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/edit_borrowed/{id}", name="edit_borrowed")
     * @param                                 Request $request
     * @param                                 EntityManagerInterface $entityManager
     * @param                                 Borrowed $borrowedId
     * @return                                \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editBorrowed(Borrowed $borrowedId, Request $request, EntityManagerInterface $entityManager)
    {
        $edit = true;
        $borrowed = new Borrowed();
        $borrowed->setBorrowDate(new \DateTime('now'));
        $borrowed->setReturnDate($borrowedId->getReturnDate());
        $borrowed->setUser($borrowedId->getUser());


        $form = $this->createForm(BorrowedFormType::class, $borrowed);


        $form->handleRequest($request);

        if ($this->isGranted('ROLE_USER') && $form->isSubmitted()) {
            /**
             * @var Borrowed $borrowed
             */
            $borrowed = $form->getData();

            if ($borrowed->getUser() !== $borrowedId->getUser()) {
                $borrowedId->getUser()->setHasBooks(false);
            }

            $userId = $borrowed->getUser();
            $user = $entityManager->find(User::class, $userId);
            $user->setHasBooks(true);

            /**
             * @var BorrowedBooks $borrowedBook
             */
            foreach ($borrowed->getBorrowedBooks() as $borrowedBook) {
                $borrowedBook->getBook()->setBorrowedQuantity($borrowedBook->getBook()->getBorrowedQuantity() + 1);
                $borrowedBook->getBook()->setQuantity($borrowedBook->getBook()->getQuantity() - 1);
                if ($borrowedBook->getBook()->getQuantity() === 0) {
                    $borrowedBook->getBook()->setAvailable(false);
                }

                $borrowedId->addBorrowedBook($borrowedBook);
            }

            $borrowed->setId($borrowedId->getId());
            $entityManager->merge($borrowed);
            try {
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'Date fields cannot be empty!');
                return $this->redirectToRoute('book_index');
            }


            $this->addFlash('success', 'Nice!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/lend_book.html.twig',
            [
                'form' => $form->createView(),
                'borrowed' => $borrowedId,
                'edit' => $edit
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/lend_reserved_book/{user}/{book}/{reservation}",
     *     name="lend_reserved_book")
     * @param User $user
     * @param Book $book
     * @param EntityManagerInterface $em
     * @param ReservationRepository $reservationRepository
     * @param Reservation $reservation
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function lendReservedBook(
        User $user,
        Book $book,
        EntityManagerInterface $em,
        ReservationRepository $reservationRepository,
        Reservation $reservation
    ) {
        $borrowed = new Borrowed();
        $borrowed->setBorrowDate(new \DateTime('now'));
        $borrowed->setReturnDate(new \DateTime('now + 15 day'));
        $borrowed->setUser($user);
        $borrowed->setActive(true);
        $book->setBorrowedQuantity($book->getBorrowedQuantity() + 1);
        $book->setQuantity($book->getQuantity() - 1);

        if ($book->getQuantity() === 0) {
            $book->setAvailable(false);
        }

        $borrowedBook = new BorrowedBooks();
        $borrowedBook->setCreatedAt('now');
        $borrowedBook->setBook($book);
        $borrowedBook->setBorrowed($borrowed);
        $reservation->setActive(false);
        $reservation->setBook(null);
        $reservation->setUser(null);
        $user->setHasBooks(true);
        $borrowed->addBorrowedBook($borrowedBook);
        $em->persist($borrowedBook);
        $em->persist($borrowed);
        $em->persist($user);
        $em->persist($reservation);
        $em->persist($book);
        $em->flush();


        $reservations = $reservationRepository->findBy(['active' => true]);

        $this->addFlash('success', 'Success!');
        return $this->render(
            'reservation/reservations.html.twig',
            [
            'reservations' => $reservations
            ]
        );
    }
}
