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
use App\Entity\Customer;
use App\Entity\User;
use App\Form\BorrowedFormType;
use App\Repository\BorrowedRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class BorrowedController extends AbstractController
{
    /**
     * @Route("/employee/borrowed_books", name="borrowed_books")
     * @param BorrowedRepository $borrowedRepository
     * @return Response
     */
    public function borrowedBooks(BorrowedRepository $borrowedRepository)
    {

        $borrowedBooks = $borrowedRepository->findBy(['active' => true]);


        return $this->render('book/borrowed_books.html.twig', [

            'borrowed' => $borrowedBooks

        ]);
    }

    /**
     * @Route("/employee/return_books/{id}", name="return_books")
     * @param Borrowed $borrowedId
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function returnBooks(Borrowed $borrowedId, EntityManagerInterface $entityManager)
    {
        /** Borrowed $borrowedId */

        foreach ($borrowedId->getBorrowedBooks() as $borrowedBook) {
            $borrowedBook->getBook()->setQuantity($borrowedBook->getBook()->getQuantity()+1);
            $borrowedBook->getBook()->setBorrowedQuantity($borrowedBook->getBook()->getBorrowedQuantity()-1);
            if($borrowedBook->getBook()->getQuantity() > 0){
                $borrowedBook->getBook()->setAvailable(true);
            }


        }
        $borrowedId->setActive(false);
        $user = $entityManager->find(User::class, $borrowedId->getUser());
        $user->setHasBooks(false);
        $entityManager->merge($borrowedId);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned all books!');
        return $this->redirectToRoute('borrowed_books');
    }

    /**
     * @Route("/employee/return_single_book/{id}/{borrowedId}/{borrowedBooks}", name= "return_single_book")
     * @param Book $book
     * @param Borrowed $borrowedId
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function returnSingleBook(BorrowedBooks $borrowedBooks, Book $book, Borrowed $borrowedId, EntityManagerInterface $entityManager)
    {
        $book->setQuantity($book->getQuantity()+1);
        $book->setBorrowedQuantity($book->getBorrowedQuantity()-1);
        $borrowedId->removeBorrowedBook($borrowedBooks);
        if($book->getQuantity() > 0){
            $book->setAvailable(true);
        }

        $user = $entityManager->find(User::class, $borrowedId->getUser());

        if(count($borrowedId->getBorrowedBooks()) === 0){
            $borrowedId->setActive(false);
            $user->setHasBooks(false);
        }

        $entityManager->merge($borrowedId);
        $entityManager->merge($book);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned one book!');
        return $this->redirectToRoute('borrowed_books');


    }

    /**
     * @Route("/employee/books_details/{id}", name="books_details")
     * @param Borrowed $borrowed
     * @return Response
     */
    public function booksDetails(Borrowed $borrowed)
    {
        return $this->render('book/books_details.html.twig', [
            'borrowed' => $borrowed
        ]);
    }

    /**
     * @Route("/employee/edit_borrowed/{id}", name="edit_borrowed")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Borrowed $borrowedId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editBorrowed(Borrowed $borrowedId, Request $request, EntityManagerInterface $entityManager)
    {

        $borrowed = new Borrowed();
        $borrowed->setBorrowDate(new \DateTime('now'));
        $borrowed->setReturnDate($borrowedId->getReturnDate());
        $form = $this->createForm(BorrowedFormType::class, $borrowed);

        $form->handleRequest($request);

        if ($this->isGranted('ROLE_USER') && $form->isSubmitted()) {
            /** @var Borrowed $borrowed */
            $borrowed = $form->getData();

            if ($borrowed->getUser() !== $borrowedId->getUser()) {
                $borrowedId->getUser()->setHasBooks(false);
            }
            $userId = $borrowed->getUser();
            $user = $entityManager->find(User::class, $userId);
            $user->setHasBooks(true);

            /** @var BorrowedBooks $borrowedBook */
            foreach ($borrowed->getBorrowedBooks() as $borrowedBook) {
                $borrowedBook->getBook()->setBorrowedQuantity($borrowedBook->getBook()->getBorrowedQuantity()+1);
                $borrowedBook->getBook()->setQuantity($borrowedBook->getBook()->getQuantity()-1);
                if($borrowedBook->getBook()->getQuantity()===0){
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

        return $this->render('book/lend_book.html.twig', [
            'form' => $form->createView()
        ]);
    }


}