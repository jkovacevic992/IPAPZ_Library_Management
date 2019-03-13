<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 24.02.19.
 * Time: 21:26
 */

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreFormType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{


    /**
     * @Route("/employee/new_genre", name="new_genre")
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

        return $this->render('genre/new_genre.html.twig', [
            'genreForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/employee/genres", name="genres")
     * @param GenreRepository $genreRepository
     * @return Response
     */
    public function genres(GenreRepository $genreRepository)
    {

        $genres = $genreRepository->findAll();

        return $this->render('genre/genres.html.twig', [

            'genres' => $genres

        ]);
    }

    /**
     * @Route("/employee/edit_genre/{id}", name="edit_genre")
     * @param Genre $genreId
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * * @return Response
     */
    public function editGenre(Genre $genreId, Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(GenreFormType::class, $genreId);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {


            $entityManager->flush();

            $this->addFlash('success', 'Genre edited!');
            return $this->redirectToRoute('genres');
        }

        return $this->render('genre/edit_genre.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/employee/genre_delete/{id}", name="genre_delete")
     * @param Genre $genre
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteGenre(Genre $genre, EntityManagerInterface $entityManager)
    {

        if ($genre->getBookGenre()[0] === null) {
            $entityManager->remove($genre);
            $entityManager->flush();
            $this->addFlash('success', 'Genre deleted!');
            return $this->redirectToRoute('book_index');
        } else {

            $this->addFlash('warning', 'Some books are using this genre and it cannot be deleted! Sorry!');
            return $this->redirectToRoute('book_index');

        }
    }

}