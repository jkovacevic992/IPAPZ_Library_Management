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

use App\Form\BookFormType;
use App\Form\BorrowedFormType;

use App\Repository\BookRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $availableBooks = $bookRepository->count(['available' => true]);
        $borrowedBooks = $bookRepository->count(['available' => false]);

        return $this->render('book/index.html.twig', [

            'books' => $books,
            'totalCustomers' => $customers,
            'totalBooks' => $bookNumber,
            'availableBooks' => $availableBooks,
            'allBorrowedBooks' => $borrowedBooks
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
            $files = $request->files->get('book_form')['images'];
            $uploads_directory = $this->getParameter('images_directory');
            $images = [];
            foreach ($files as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploads_directory,
                    $fileName
                );
                $images[] = $fileName;


            }
            if($images !== null){
                $book->setImages($images);
            }



            $book->setUser($this->getUser());
            $entityManager->persist($book);

            $entityManager->flush();

            $this->addFlash('success', 'New book submitted!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new_book.html.twig', [
            'form' => $form->createView()
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
            foreach ($borrowed->getBorrowedBooks() as $borrowedBook) {
                $borrowedBook->getBook()->setAvailable(false);
            }
            $entityManager->persist($borrowed);

            $entityManager->flush();

            $this->addFlash('success', 'Nice!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/lend_book.html.twig', [
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

        $form = $this->createForm(BookFormType::class, $bookId);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            // $book = $entityManager->find(Book::class, $bookId->getId() );
            /** @var Book $book */
            $book = $form->getData();
            $images = [];
            $files = $request->files->get('book_form')['images'];
            $uploads_directory = $this->getParameter('images_directory');

            foreach ($files as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploads_directory,
                    $fileName
                );
                $images[] = $fileName;


            }

            $book->setImages($images);

            $entityManager->flush();

            $this->addFlash('success', 'Book edited!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/book_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/view_book/{id}", name="book_view")
     * @param Book $book
     * @return Response
     */
    public function showBook(Book $book)
    {


        return $this->render('book/view_book.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/set_image/{id}/{imageName}", name="set_main_image")
     * @param Book $book
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function setMainImage(Book $book, String $imageName, EntityManagerInterface $entityManager)
    {


        $images = $book->getImages();
        $key = array_search($imageName, $images);
        unset($images[$key]);
        array_unshift($images, $imageName);
        $book->setImages($images);
        $entityManager->flush();

        return $this->redirectToRoute('book_index');
    }
}