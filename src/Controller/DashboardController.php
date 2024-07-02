<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\ProfilePassType;
// use App\Form\ProfileType;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\HttpFoundation\Request;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CustomerRepository $customerRepository, InvoiceRepository $invoiceRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        // widget Products
        $products = $productRepository->getTotalProductsByMonth($this->getUser());

        $products['lastweekPercentage'] = $products['total'] > 0 ? round(($products['lastweek'] / $products['total']) * 100) : 0;

        // widget customers
        $customers = $customerRepository->getTotalCustomersByMonth($this->getUser());
        
        $customers['lastweekPercentage'] = $customers['total'] > 0 ? round(($customers['lastweek'] / $customers['total']) * 100) : 0;

        // wiget invoices
        $invoices = $invoiceRepository->getTotalInvoicesByMonth($this->getUser());

        $invoices['lastweekPercentage'] = $invoices['total'] > 0 ? round(($invoices['lastweek'] / $invoices['total']) * 100) : 0;

        // widget invoices total paid amount by month
        $getTotalPaidAmountByMonth = $invoiceRepository->getTotalPaidAmountByMonth($this->getUser());

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'productsWidget' => $products,
            'customersWidget' => $customers,
            'invoicesWidget' => $invoices,
            'getTotalPaidAmountByMonth' => $getTotalPaidAmountByMonth,
        ]);
    }

    #[Route('/profile', name: 'app_dashboard_profile', methods: ['GET'])]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        $ProfilePassForm = $this->createForm(ProfilePassType::class, $user);
        $ProfilePassForm->handleRequest($request);

        return $this->render('dashboard/profile.html.twig', [
            'user' => $user,
            'profileForm' => $profileForm,
            'profilePassForm' => $ProfilePassForm
        ]);
    }

    #[Route('/profile/edit', name: 'app_dashboard_profile_edit', methods: ['POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_dashboard_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/profile/edit/password', name: 'app_dashboard_profile_edit_password', methods: ['POST'])]
    public function editPassword(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user_pass_hash = $this->getUser()->getPassword();

        $user_id = $request->query->get('id');

        if ($user_id != $this->getUser()->getId()) {
            return $this->redirectToRoute('app_dashboard_profile', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ProfilePassType::class, $this->getUser());
        $form->handleRequest($request);

        if (!password_verify($request->getPayload()->get('Currentpassword'), $user_pass_hash))
        {
            $this->addFlash('error', 'Current password is not valid');
            return $this->redirectToRoute('app_dashboard_profile', [], Response::HTTP_SEE_OTHER);
        }
        if ($form->get('password')->getData() != $request->getPayload()->get('Confirmpassword'))
        {
            $this->addFlash('error', 'New password and confirm password are not the same');
            return $this->redirectToRoute('app_dashboard_profile', [], Response::HTTP_SEE_OTHER);
        }
        
        $this->getUser()->setPassword($form->get('password')->getData());

        $entityManager->persist($this->getUser());
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard_profile', [], Response::HTTP_SEE_OTHER);
    }
}
