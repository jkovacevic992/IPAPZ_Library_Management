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
use App\Repository\BorrowedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class BorrowedController extends AbstractController
{
    /**
     * @Route("/borrowed_books", name="borrowed_books")

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
     * @Route("/return_books/{id}", name="return_books")
     * @param Borrowed $borrowedId
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function returnBooks(Borrowed $borrowedId, EntityManagerInterface $entityManager)
    {
        /** Borrowed $borrowedId */

        foreach ($borrowedId->getBorrowedBooks() as $borrowedBook) {
            $borrowedBook->getBook()->setAvailable(true);

        }
        $borrowedId->setActive(false);
        $entityManager->merge($borrowedId);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned all books!');
        return $this->redirectToRoute('book_index');
    }

    /**
     * @Route("/return_single_book/{id}", name= "return_single_book")
     * @param Book $book
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function returnSingleBook(Book $book,  EntityManagerInterface $entityManager)
    {
        $book->setAvailable(true);

        $entityManager->merge($book);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned one book!');
        return $this->redirectToRoute('book_index');


    }
    /**
     * @Route("/books_details/{id}", name="books_details")
     * @param Borrowed $borrowed
     * @return Response
     */
    public function booksDetails(Borrowed $borrowed)
    {
        return $this->render('book/books_details.html.twig',[
            'borrowed' => $borrowed
        ]);
    }


}