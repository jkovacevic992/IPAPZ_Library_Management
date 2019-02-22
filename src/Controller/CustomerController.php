<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 19.02.19.
 * Time: 12:41
 */

namespace App\Controller;




use App\Entity\Customer;
use App\Form\CustomerFormType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{




    /**
     * @Route("/customers", name="customers")

     * @param CustomerRepository $customerRepository
     * @return Response
     */
    public function customers(CustomerRepository $customerRepository)
    {

        $customers = $customerRepository->findAll();

        return $this->render('customer/customers.html.twig', [

            'customers' => $customers

        ]);
    }
    /**
     * @Route("/new_customer", name="new_customer")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function newCustomer( Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(CustomerFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Customer $customer */
            $customer = $form->getData();

            $entityManager->persist($customer);

            $entityManager->flush();

            $this->addFlash('success', 'New customer added!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('customer/new_customer.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/view/{id}", name="customer_view")
     * @param Customer $customer
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * * @return Response
     */
    public function show(Customer $customer)
    {


        return $this->render('customer/view.html.twig',[
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/customer_change/{id}", name="customer_change")
     * @param Customer $customerId
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * * @return Response
     */
    public function changeInfo(Customer $customerId, Request $request, EntityManagerInterface $entityManager)
    {
        $customer = new Customer();
        $customer->setFirstName($customerId->getFirstName());
        $customer->setLastName($customerId->getLastName());
        $form = $this->createForm(CustomerFormType::class, $customer);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Customer $customer */
            $customer = $form->getData();
            $customer->setId($customerId->getId());
            $entityManager->merge($customer);

            $entityManager->flush();

            $this->addFlash('success', 'Customer edited!');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('customer/customer_change.html.twig',[
            'form' => $form->createView()
        ]);
    }
}