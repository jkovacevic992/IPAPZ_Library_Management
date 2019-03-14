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


use App\Entity\User;
use App\Form\BookFormType;
use App\Form\BorrowedFormType;

use App\Repository\BookRepository;

use App\Repository\UserRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookController extends AbstractController
{


    /**
     * @Route("/employee/new_book", name="new_book")
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
            if ($images !== null) {
                $book->setImages($images);
            }

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
     * @Route("/employee/lend_book", name="lend_book")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */


    public function lendBook(Request $request, EntityManagerInterface $entityManager)
    {
        $borrowed = new Borrowed();
        try {
            $borrowed->setBorrowDate(new \DateTime('now'));
        }catch (\Exception $e){
            $e->getMessage();
        }
        $form = $this->createForm(BorrowedFormType::class, $borrowed);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Borrowed $borrowed */
            $borrowed = $form->getData();
            $userId = $borrowed->getUser();
            $user = $entityManager->find(User::class, $userId);
            $user->setHasBooks(true);
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
     * @Route("/employee/edit_book/{id}", name="edit_book")
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

            /** @var Book $book */
            $book = $form->getData();
            $book->setId($bookId->getId());
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


            $entityManager->merge($book);
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
     * @Route("/employee/set_image/{id}/{imageName}", name="set_main_image")
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

    /**
     * @Route("/employee/book_delete/{id}", name="book_delete")
     * @param Book $book
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteBook(Book $book, EntityManagerInterface $entityManager)
    {

        if ($book->getAvailable() === false) {
            $this->addFlash('warning', 'Book has to be returned first.');
            return $this->redirectToRoute('book_index');
        } else {
            $entityManager->remove($book);
            $entityManager->flush();
            $this->addFlash('success', 'Book deleted!');
            return $this->redirectToRoute('book_index');

        }
    }

    /**
     * @Route("/", methods={"GET","POST"}, name="book_index")
     * @param BookService $query
     * @param BookRepository $bookRepository
     * @param UserRepository $cuserRepository
     * @param Request $request
     * @return Response
     */
    public function listBookAction(Request $request, BookService $query, BookRepository $bookRepository, UserRepository $userRepository)
    {
        $users = $userRepository->findUsers()[0][1];
        $bookNumber = $bookRepository->findBooks()[0][1];
        $availableBooks = $bookRepository->count(['available' => true]);
        $borrowedBooks = $bookRepository->count(['available' => false]);


        $formSearch = $this->createFormBuilder(null)
            ->add('query', SearchType::class)
            ->add('search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();
        $formSearch->handleRequest($request);
        if($formSearch->isSubmitted() && $formSearch->isValid()) {

            $books = $query->returnFoundBooks($request,$formSearch->getData()['query']);

            return $this->render('book/index.html.twig', [
                'form' => $formSearch->createView(),
                'books' => $books,
                'formSearch' => $formSearch->createView(),
                'totalUsers' => $users,
                'totalBooks' => $bookNumber,
                'availableBooks' => $availableBooks,
                'allBorrowedBooks' => $borrowedBooks
            ]);
        }else{
            $books = $query->returnBooks($request);
            return $this->render('book/index.html.twig', [
                'books' => $books,
                'totalUsers' => $users,
                'totalBooks' => $bookNumber,
                'availableBooks' => $availableBooks,
                'allBorrowedBooks' => $borrowedBooks,
                'formSearch' => $formSearch->createView()

            ]);
        }




    }


}