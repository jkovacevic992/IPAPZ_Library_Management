<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 18.02.19.
 * Time: 13:46
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;

use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @Route("admin/register", name="app_register")
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

        $users = $userRepository->findBy(['admin' => false]);

        return $this->render('employee/employees.html.twig', [

            'users' => $users

        ]);
    }

    /**
     * @Route("/profile/view_employee/{id}", name="employee_view")
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
     * @Route("/profile/employee_change/{id}", name="employee_change")
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
     * @Route("/profile/employee_delete/{id}", name="employee_delete")
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
}