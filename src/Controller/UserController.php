<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 18.02.19.
 * Time: 13:46
 */

namespace App\Controller;

use App\Entity\Book;


use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Form\RegistrationFormType;

use App\Form\UserWishlistFormType;
use App\Repository\BorrowedRepository;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param AppCustomAuthenticator $authenticator
     * @param EntityManagerInterface $entityManager
     * @return null|Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        AppCustomAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    )
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
//            return $guardHandler->authenticateUserAndHandleSuccess(
//                $user,
//                $request,
//                $authenticator,
//                'main' // firewall name in security.yaml
//            );
            return $this->redirectToRoute('book_index');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }


    /**
     * @Route("/admin/users", name="users")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function users(UserRepository $userRepository)
    {

        $users = $userRepository->findBy(['admin' => false, 'employee' => false]);

        return $this->render('user/users.html.twig', [

            'users' => $users

        ]);
    }

    /**
     * @Route("/admin/employees", name="employees")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function employees(UserRepository $userRepository)
    {

        $users = $userRepository->findBy(['employee' => 'true']);

        return $this->render('employee/employees.html.twig', [

            'users' => $users

        ]);
    }

    /**
     * @Route("/admin/view_employee/{id}", name="employee_view")
     * @param User $user
     * @return Response
     */
    public function viewUser(User $user)
    {


        return $this->render('employee/employee_view.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/employee_change/{id}", name="employee_change")
     * @param User $userId
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * * @return Response
     */
    public function editUser(User $userId, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {

        $form = $this->createForm(RegistrationFormType::class, $userId);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->find(User::class, $userId->getId());
            if ($userId->getEmail() === $form['email']->getData()) {
                /** @var User $user */
                $user->setFirstName($form['firstName']->getData());
                $user->setLastName($form['lastName']->getData());
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    ));
            } else {
                $user = $form->getData();
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    ));
                $entityManager->persist($user);

            }


            $entityManager->flush();

            $this->addFlash('success', 'Employee edited!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('employee/employee_change.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/employee_delete/{id}", name="employee_delete")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'Employee deleted!');
        return $this->redirectToRoute('book_index');
    }

    /**
     * @Route("/admin/make_employee/{id}", name="make_employee")
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function makeEmployee(User $user, EntityManagerInterface $entityManager)
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->update('App\Entity\User', 'u')
            ->set('u.roles', '\'["ROLE_EMPLOYEE"]\'')
            ->set('u.employee', 1)
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult();
        return $this->redirectToRoute('book_index');

    }

    /**
     * @Route("/profile/my_borrowed_books", name="my_borrowed_books")
     * @param UserInterface $user
     * @return Response
     */
    public function usersBorrowedBooks(EntityManagerInterface $entityManager,Request $request, UserInterface $user, BorrowedRepository $borrowedRepository)
    {

        $lateFee = [];
        $borrowed = $borrowedRepository->findBy(['user' => $user->getId(), 'active' => true]);

        //DOVRŠITI

        foreach ($borrowed as $borrowedBooks) {

            $time = $borrowedBooks->getReturnDate();
            $timeDiff = date_diff(new \DateTime('now'), $time)->d ;
            if($time < new \DateTime('now')) {
                $lateFee[$borrowedBooks->getId()] = sprintf("%.2f",$timeDiff* 0.5 * count($borrowedBooks->getBorrowedBooks()));

            }else{
                $lateFee[$borrowedBooks->getId()] = sprintf("%.2f",0);
            }


        }




        return $this->render('user/my_borrowed_books.html.twig', [

            'books' => $borrowed,
            'lateFee' => $lateFee


        ]);

    }

    /**
     * @Route("/profile/add_book/{id}", name="add_book")
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */


    public function addBookToWishlist(UserInterface $user, Book $book, EntityManagerInterface $entityManager)
    {

        $wishlist = new Wishlist();
        $wishlist->setUser($user);
        $wishlist->setBook($book);
        foreach ($user->getWishlist() as $existingWishlist){
            if($existingWishlist->getBook() === $wishlist->getBook() && $existingWishlist->getUser() === $wishlist->getUser()){
                $this->addFlash('warning', 'This book is already in your wish list!');
                return $this->redirectToRoute('book_index');
            }
        }
        $user->addWishlist($wishlist);

        $entityManager->persist($user);

        $entityManager->flush();


        return $this->redirectToRoute('book_index');
    }
    /**
     * @Route("/profile/remove_from_wishlist/{id}", name="remove_from_wishlist")
     * @param EntityManagerInterface $entityManager
     * @param Wishlist $wishlist
     * @return RedirectResponse
     */
    public function removeFromWishlist(UserInterface $user, Wishlist $wishlist, EntityManagerInterface $entityManager)
    {

        $user->removeWishlist($wishlist);
        $entityManager->merge($user);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully removed the book from wish list!');
        return $this->redirectToRoute('book_index');
    }


    /**
     * @Route("/profile/my_wishlist", name="my_wishlist")
     * @param User $user
     * @return Response
     */
    public function usersWishlist(UserInterface $user)
    {
        $tmp = [];
        $wishlist = $user->getWishlist();

        foreach ($wishlist as $books) {

            $tmp[] = $books;

        }


        return $this->render('user/wishlist.html.twig', [

            'books' => $tmp

        ]);


    }

    /**
     * @Route("/profile/reserve_book/{id}", name="reserve_book")
     * @param EntityManagerInterface $entityManager
     * @param UserInterface $user
     * @param Book $book
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function reserveBook(UserInterface $user, Book $book, EntityManagerInterface $entityManager)
    {

        $response = $this->redirectToRoute('book_index');


        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setBook($book);
        $reservation->setCreatedAt(new \DateTime('now'));
        $book->setNotification(true);
        $user->addReservation($reservation);

        $entityManager->persist($book);
        $entityManager->persist($user);

        $entityManager->flush();


        $this->addFlash('success', 'Reservation successful!');
        return $response;
    }

    public function calculateLateFee($book)
    {
        $time = $book->getBorrowed()->getReturnDate();
        if($time < new \DateTime('now')){
        $fee = date_diff(new \DateTime('now'), $book->getBorrowed()->getReturnDate())->d*0.5;


        return $fee;

    }
    }









}

