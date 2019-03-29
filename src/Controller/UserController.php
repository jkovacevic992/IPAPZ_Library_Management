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
use App\Repository\BorrowedRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/login", name="app_login")
     * @param           AuthenticationUtils $authenticationUtils
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/register", name="app_register")
     * @param              Request $request
     * @param              UserPasswordEncoderInterface $passwordEncoder
     * @param              EntityManagerInterface $entityManager
     * @return             null|\Symfony\Component\HttpFoundation\Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
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

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/employee/users", name="users")
     * @param                 UserRepository $userRepository
     * @return                \Symfony\Component\HttpFoundation\Response
     */
    public function users(UserRepository $userRepository)
    {

        $users = $userRepository->findBy(['admin' => false, 'employee' => false]);

        return $this->render(
            'user/users.html.twig',
            [

                'users' => $users

            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/employees", name="employees")
     * @param                     UserRepository $userRepository
     * @return                    \Symfony\Component\HttpFoundation\Response
     */
    public function employees(UserRepository $userRepository)
    {

        $users = $userRepository->findBy(['employee' => 'true']);

        return $this->render(
            'employee/employees.html.twig',
            [

                'users' => $users

            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/view_employee/{id}", name="employee_view")
     * @param                              User $user
     * @return                             \Symfony\Component\HttpFoundation\Response
     */
    public function viewUser(User $user)
    {


        return $this->render(
            'employee/employee_view.html.twig',
            [
                'user' => $user
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/employee_change/{id}", name="employee_change")
     * @param                                User $userId
     * @param                                Request $request
     * @param                                EntityManagerInterface $entityManager
     * @param                                UserPasswordEncoderInterface $passwordEncoder
     * * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editUser(
        User $userId,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {

        $form = $this->createForm(RegistrationFormType::class, $userId);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->find(User::class, $userId->getId());
            if ($userId->getEmail() === $form['email']->getData()) {
                /**
                 * @var User $user
                 */
                $user->setFirstName($form['firstName']->getData());
                $user->setLastName($form['lastName']->getData());
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            } else {
                $user = $form->getData();
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager->persist($user);
            }


            $entityManager->flush();

            $this->addFlash('success', 'User edited!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'employee/employee_change.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/employee_delete/{id}", name="employee_delete")
     * @param                                User $user
     * @param                                EntityManagerInterface $entityManager
     * @return                               \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager)
    {
        if ($user->getHasBooks() === true) {
            $this->addFlash('warning', 'User has to return their books first!');
            return $this->redirectToRoute('book_index');
        } else {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted!');
            return $this->redirectToRoute('book_index');
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/admin/make_employee/{id}", name="make_employee")
     * @param                              User $user
     * @param                              EntityManagerInterface $entityManager
     * @return                             \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @Symfony\Component\Routing\Annotation\Route("/profile/my_borrowed_books", name="my_borrowed_books")
     * @param                               UserInterface $user
     * @param BorrowedRepository $borrowedRepository
     * @param PaymentMethodRepository $paymentMethodRepository
     * @return                              \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function usersBorrowedBooks(
        UserInterface $user,
        BorrowedRepository $borrowedRepository,
        PaymentMethodRepository $paymentMethodRepository
    ) {

        $lateFee = [];
        $borrowed = $borrowedRepository->findBy(['user' => $user->getId(), 'active' => true]);
        $paymentMethods = $paymentMethodRepository->findAll();


        foreach ($borrowed as $borrowedBooks) {
            $time = $borrowedBooks->getReturnDate();
            $timeDiff = date_diff(new \DateTime('now'), $time)->d;
            if ($time < new \DateTime('now')) {
                $lateFee[$borrowedBooks->getId()] = sprintf(
                    "%.2f",
                    $timeDiff * 0.5 * count($borrowedBooks->getBorrowedBooks())
                );
            } else {
                $lateFee[$borrowedBooks->getId()] = sprintf("%.2f", 0);
            }
        }


        return $this->render(
            'user/my_borrowed_books.html.twig',
            [

                'books' => $borrowed,
                'lateFee' => $lateFee,
                'paymentMethods' => $paymentMethods


            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/add_book/{id}", name="add_book")
     * @param UserInterface $user
     * @param Book $book
     * @param                           EntityManagerInterface $entityManager
     * @return                          \Symfony\Component\HttpFoundation\RedirectResponse
     */


    public function addBookToWishlist(UserInterface $user, Book $book, EntityManagerInterface $entityManager)
    {

        $wishlist = new Wishlist();
        $wishlist->setUser($user);
        $wishlist->setBook($book);
        foreach ($user->getWishlist() as $existingWishlist) {
            if ($existingWishlist->getBook() === $wishlist->getBook() &&
                $existingWishlist->getUser() === $wishlist->getUser()) {
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
     * @Symfony\Component\Routing\Annotation\Route("/profile/remove_from_wishlist/{id}", name="remove_from_wishlist")
     * @param UserInterface $user
     * @param                                       Wishlist $wishlist
     * @param                                       EntityManagerInterface $entityManager
     * @return                                      \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromWishlist(UserInterface $user, Wishlist $wishlist, EntityManagerInterface $entityManager)
    {

        if ($user === $wishlist->getUser()) {
            $user->removeWishlist($wishlist);
            $entityManager->merge($user);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully removed the book from wish list!');
            return $this->redirectToRoute('book_index');
        } else {
            $this->addFlash('warning', 'Not authorized!');
            return $this->redirectToRoute('book_index');
        }
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/my_wishlist", name="my_wishlist")
     * @param UserInterface $user
     * @return                        \Symfony\Component\HttpFoundation\Response
     */
    public function usersWishlist(UserInterface $user)
    {
        $tmp = [];
        $wishlist = $user->getWishlist();

        foreach ($wishlist as $books) {
            $tmp[] = $books;
        }


        return $this->render(
            'user/wishlist.html.twig',
            [

                'books' => $tmp

            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/my_reservations", name="my_reservations")
     * @param UserInterface $user
     * @return                        \Symfony\Component\HttpFoundation\Response
     */
    public function usersReservations(UserInterface $user)
    {
        $tmp = [];
        $reservations = $user->getReservation();

        foreach ($reservations as $reservation) {
            $tmp[] = $reservation;
        }


        return $this->render(
            'user/my_reservations.html.twig',
            [

                'reservations' => $tmp

            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/profile/reserve_book/{id}", name="reserve_book")
     * @param                               UserInterface $user
     * @param                               Book $book
     * @param                               EntityManagerInterface $entityManager
     * @return                              \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
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
        if ($time < new \DateTime('now')) {
            $fee = date_diff(new \DateTime('now'), $book->getBorrowed()->getReturnDate())->d * 0.5;


            return $fee;
        }
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/username", name="username")
     * @param UserInterface $user
     * @return JsonResponse
     */
    public function username(UserInterface $user)
    {
        $username = $user->getUsername();
        return new JsonResponse(
            [
            'username' => $username
            ]
        );
    }
}
