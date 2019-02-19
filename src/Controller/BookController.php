<?php
/**
 * Created by PhpStorm.
 * Customer: josip
 * Date: 18.02.19.
 * Time: 12:49
 */

namespace App\Controller;


use App\Entity\Book;
use App\Entity\Genre;
use App\Form\BookFormType;
use App\Form\BorrowedFormType;
use App\Form\GenreFormType;
use App\Repository\BookRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
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

        return $this->render('book/index.html.twig', [

            'books' => $books,
            'customers' => $customers,
            'bookNumber' => $bookNumber
        ]);
    }

    /**
     * @Route("/new_book", name="new_book")
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
     * @Route("/new_genre", name="new_genre")
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
     * @Route("/lend_book", name="lend_book")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function lendBook(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(BorrowedFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Genre $genre */
            $borrowed = $form->getData();
            $entityManager->persist($borrowed);

            $entityManager->flush();

            $this->addFlash('success', 'Nice!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/lend_book.html.twig',[
            'bookForm' => $form->createView()
        ]);
    }
}