<?php
/**
 * Created by PhpStorm.
 * Customer: josip
 * Date: 18.02.19.
 * Time: 12:49
 */

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\UserRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{


    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/new_book", name="new_book")
     * @param                       Request $request
     * @param                       EntityManagerInterface $entityManager
     * @return                      \Symfony\Component\HttpFoundation\Response
     */
    public function newBook(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(BookFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /**
             * @var Book $book
             */
            $book = $form->getData();
            $files = $request->files->get('book_form')['images'];
            $uploadsDirectory = $this->getParameter('images_directory');
            $images = [];
            foreach ($files as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploadsDirectory,
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

        return $this->render(
            'book/new_book.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }




    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/edit_book/{id}", name="edit_book")
     * @param                             Book $bookId
     * @param                             Request $request
     * @param                             EntityManagerInterface $entityManager
     * * @return                          \Symfony\Component\HttpFoundation\Response
     */
    public function editBook(Book $bookId, Request $request, EntityManagerInterface $entityManager)
    {


        $form = $this->createForm(BookFormType::class, $bookId);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {

            /**
             * @var Book $book
             */
            $book = $form->getData();
            $book->setId($bookId->getId());
            $images = [];
            $files = $request->files->get('book_form')['images'];
            $uploadsDirectory = $this->getParameter('images_directory');

            foreach ($files as $file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploadsDirectory,
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

        return $this->render(
            'book/book_edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/view_book/{id}", name="book_view")
     * @param                    Book $book
     *
     * @param UserRepository $userRepository
     * @return                   \Symfony\Component\HttpFoundation\Response
     */
    public function showBook(Book $book, UserRepository $userRepository)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $borrowedByUser = false;

        if ($user === 'anon.') {
            return $this->render(
                'book/view_book.html.twig',
                [
                    'book' => $book,
                    'borrowedByUser' => $borrowedByUser
                ]
            );
        } else {
            $temp = $userRepository->findBook($user->getId(), $book->getId());
            if (in_array($book, $temp)) {
                $borrowedByUser = true;
            }

            return $this->render(
                'book/view_book.html.twig',
                [
                    'book' => $book,
                    'borrowedByUser' => $borrowedByUser
                ]
            );
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/set_image/{id}/{imageName}", name="set_main_image")
     * @param                                         Book $book
     * @param $imageName
     * @param                                         EntityManagerInterface $entityManager
     * @return                                        \Symfony\Component\HttpFoundation\Response
     */
    public function setMainImage(Book $book, $imageName, EntityManagerInterface $entityManager)
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
     * @Symfony\Component\Routing\Annotation\Route("/employee/book_delete/{id}", name="book_delete")
     * @param                               Book $book
     * @param                               EntityManagerInterface $entityManager
     * @return                              \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteBook(Book $book, EntityManagerInterface $entityManager)
    {


        if ($book->getAvailable() === false || $book->getBorrowedQuantity() > 0) {
            $this->addFlash('warning', 'All copies of the book have to be returned first!');
            return $this->redirectToRoute('book_index');
        }

        $entityManager->remove($book);
        $entityManager->flush();
        $this->addFlash('success', 'Book deleted!');
        return $this->redirectToRoute('book_index');
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/", methods={"GET","POST"}, name="book_index")
     * @param      BookService $query
     * @param      BookRepository $bookRepository
     * @param      UserRepository $userRepository
     * @param      EntityManagerInterface $entityManager
     * @param      GenreRepository $genreRepository
     * @param      Request $request
     * @return     \Symfony\Component\HttpFoundation\Response
     */
    public function listBookAction(
        Request $request,
        BookService $query,
        BookRepository $bookRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        GenreRepository $genreRepository
    ) {
        $genres = $genreRepository->findGenresAscName();
        $users = $userRepository->findUsers()[0][1];
        $bookNumber = $bookRepository->findBooks()[0][1];
        $availableBooks = $bookRepository->count(['available' => true]);
        $user = $this->getUser();
        $book = $this->checkBookAvailability($user, $entityManager);
        $topBooks = $bookRepository->getTopBooks();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER') && $book !== false) {
            $this->addFlash('success', $book->getName() . ' is available!');
        }

        $formSearch = $this->createFormSearch();
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $books = $query->returnFoundBooks($request, $formSearch->getData()['query']);
            $topBooks = null;
        } else {
            $get = $request->query->get('genre');
            if ($get !== null) {
                $books = $query->returnBooksByGenre($request, $get);
                $topBooks = null;
            } else {
                $books = null;
            }
        }

        return $this->render(
            'book/index.html.twig',
            [
                'books' => $books,
                'totalUsers' => $users,
                'totalBooks' => $bookNumber,
                'availableBooks' => $availableBooks,
                'genres' => $genres,
                'formSearch' => $formSearch->createView(),
                'topBooks' => $topBooks

            ]
        );
    }

    public function checkBookAvailability($user, EntityManagerInterface $entityManager)
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            foreach ($user->getReservation() as $reservation) {
                if ($reservation->getBook() === null) {
                    continue;
                }

                if ($reservation->getBook()->getAvailable()) {
                    $book = $reservation->getBook();
                    $book->setNotification(false);
                    $entityManager->persist($book);
                    $entityManager->flush();
                    return $book;
                }
            }
        }

        return false;
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/all_books", name="all_books")
     * @param Request $request
     * @param BookService $query
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allBooks(Request $request, BookService $query)
    {


        $formSearch = $this->createFormSearch();
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $books = $query->returnFoundBooks($request, $formSearch->getData()['query']);
        } else {
            $books = $query->returnBooks($request);
        }

        return $this->render(
            'book/all_books.html.twig',
            [
            'books' => $books,
            'formSearch' => $formSearch->createView()
            ]
        );
    }

    public function createFormSearch()
    {
        return $formSearch = $this->createFormBuilder(null)
            ->add(
                'query',
                SearchType::class,
                [
                    'attr' => [
                        'class' => 'form-control mr-sm-2'
                    ],
                    'label' => false,

                ]
            )
            ->add(
                'search',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn purple-gradient btn-rounded btn-sm my-0'
                    ]
                ]
            )
            ->getForm();
    }
}
