<?php
/**
 * Created by PhpStorm.
 * Customer: josip
 * Date: 18.02.19.
 * Time: 12:49
 */

namespace App\Controller;


use App\Entity\Book;
use App\Entity\Borrowed;
use App\Entity\BorrowedBooks;
use App\Entity\Genre;
use App\Form\BookFormType;
use App\Form\BorrowedFormType;
use App\Form\GenreFormType;
use App\Repository\BookRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index")
     * @param BookRepository $bookRepository
     * @param CustomerRepository $customerRepository
     * @return Response
     */
    public function index(BookRepository $bookRepository, CustomerRepository $customerRepository)
    {

        $books = $bookRepository->findAll();
        $customers = $customerRepository->findCustomers()[0][1];
        $bookNumber = $bookRepository->findBooks()[0][1];
        $availableBooks = $bookRepository->count(['available'=> true]);
        $borrowedBooks = $bookRepository->count(['available'=> false]);

        return $this->render('book/index.html.twig', [

            'books' => $books,
            'customers' => $customers,
            'bookNumber' => $bookNumber,
            'availableBooks' => $availableBooks,
            'borrowedBooks' => $borrowedBooks
        ]);
    }



    /**
     * @Route("/profile/new_book", name="new_book")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function newBook(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(BookFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Book $book */
            $book = $form->getData();
            $book->setUser($this->getUser());

            $entityManager->persist($book);

            $entityManager->flush();

            $this->addFlash('success', 'New book submitted!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new_book.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/new_genre", name="new_genre")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newGenre(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(GenreFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Genre $genre */
            $genre = $form->getData();
            $entityManager->persist($genre);

            $entityManager->flush();

            $this->addFlash('success', 'New genre submitted!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('genre/new_genre.html.twig',[
            'genreForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/lend_book", name="lend_book")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response

     */


    public function lendBook(Request $request, EntityManagerInterface $entityManager)
    {
        $borrowed = new Borrowed();
        $borrowed->setBorrowDate(new \DateTime('now'));
        $form = $this->createForm(BorrowedFormType::class, $borrowed);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Borrowed $borrowed */
            $borrowed = $form->getData();
            /** @var BorrowedBooks $borrowedBook */
            foreach($borrowed->getBorrowedBooks() as $borrowedBook){
                $borrowedBook->getBook()->setAvailable(false);
            }
            $entityManager->persist($borrowed);

            $entityManager->flush();

            $this->addFlash('success', 'Nice!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/lend_book.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/edit_book/{id}", name="edit_book")
     * @param Book $bookId
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * * @return Response
     */
    public function editBook(Book $bookId, Request $request, EntityManagerInterface $entityManager)
    {
        $book = new Book();
        $book->setName($bookId->getName());
        $book->setAuthor($bookId->getAuthor());
        $book->setGenre($bookId->getGenre());
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Customer $customer */
            $book = $form->getData();
            $book->setId($bookId->getId());
            $entityManager->merge($book);

            $entityManager->flush();

            $this->addFlash('success', 'Book edited!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/book_edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
}