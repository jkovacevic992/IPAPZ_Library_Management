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
use App\Entity\Customer;
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
     * @Route("/profile/borrowed_books", name="borrowed_books")

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
     * @Route("/profile/return_books/{id}", name="return_books")
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
        $customer = $entityManager->find(Customer::class,$borrowedId->getCustomer());
        $customer->setHasBooks(false);
        $entityManager->merge($borrowedId);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned all books!');
        return $this->redirectToRoute('borrowed_books');
    }

    /**
     * @Route("/profile/return_single_book/{id}/{borrowedId}", name= "return_single_book")
     * @param Book $book
     * @param Borrowed $borrowedId
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function returnSingleBook(Book $book, Borrowed $borrowedId,  EntityManagerInterface $entityManager)
    {
        $book->setAvailable(true);


        $counter = 0;
        foreach($borrowedId->getBorrowedBooks() as $singleBook){
            if($singleBook->getBook()->getAvailable()===true){
                $counter++;

            }

        }
        if($counter === count($borrowedId->getBorrowedBooks())){
            $borrowedId->setActive(false);
            $customer = $entityManager->find(Customer::class,$borrowedId->getCustomer());
            $customer->setHasBooks(false);

        }
        $entityManager->merge($borrowedId);
        $entityManager->merge($book);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully returned one book!');
        return $this->redirectToRoute('borrowed_books');


    }
    /**
     * @Route("/profile/books_details/{id}", name="books_details")
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