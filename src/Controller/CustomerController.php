<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{   
    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(Request $request, CustomerRepository $customerRepository): Response
    {
        $listCustomer = $customerRepository->findByPage(
            $request->query->getInt('page', 1),
            10
        );

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);
        
        return $this->render('dashboard/customer.html.twig', [
            'customers' => $listCustomer["customers"],
            'total' => $listCustomer["customers"]->count(),
            'page' => $listCustomer["page"],
            'pages' => $listCustomer["pages"],
            'get' => 'page',
            'limit' => $listCustomer["limit"],

            'customer' => $customer,
            'form' => $form
        ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);
        
        $customer->setOwner($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customer);
            $entityManager->flush();

            // return $this->redirectToRoute('app_customer_get_edit', ['id' => $customer->getId()], Response::HTTP_SEE_OTHER);
            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    // public function show(Customer $customer): Response
    // {
    //     if (!$this->checkCustomer($customer)) {
    //         return $this->redirectToRoute('app_customer', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('customer/show.html.twig', [
    //         'customer' => $customer,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_customer_get_edit', methods: ['GET'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        if (!$this->checkCustomer($customer)) {
            return $this->redirectToRoute('app_customer', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        return $this->render('dashboard/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['POST'])]
    public function editSave(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        if (!$this->checkCustomer($customer)) {
            return $this->redirectToRoute('app_customer', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customer', [], Response::HTTP_SEE_OTHER);
    }

    // check if the customer is not null and the user id is equal to the current user id
    private function checkCustomer(Customer $customer): bool
    {
        return $customer !== null && $this->getUser()->getId() === $customer->getOwner()->getId();
    }
}
