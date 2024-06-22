<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CustomerRepository $customerRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        // widget Products
        $total = count($productRepository->findBy(['users' => $this->getUser()]));

        $lastweek = $productRepository->createQueryBuilder('product')
            ->select('count(product.id) as lastweek')
            ->where('product.users = :user')
            ->andWhere('product.createdAt >= :lastWeek')
            ->setParameter('user', $this->getUser())
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
            ->getSingleResult();
        
        $twoweeks = $productRepository->createQueryBuilder('product')
            ->select('count(product.id) as twoweeks')
            ->where('product.users = :user')
            ->andWhere('product.createdAt >= :twoWeeks')
            ->andWhere('product.createdAt < :lastWeek')
            ->setParameter('user', $this->getUser())
            ->setParameter('twoWeeks', new \DateTime('-2 week'))
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
            ->getSingleResult();

        $products = [
            'total' => $total,
            'lastweek' => $lastweek['lastweek'],
            'twoweeks' => $twoweeks['twoweeks'],
        ];

        $products['lastweekPercentage'] = $products['total'] > 0 ? round(($products['lastweek'] / $products['total']) * 100) : 0;

        // widget customers
        $totalCus = count($customerRepository->findBy(['owner' => $this->getUser()]));

        $lastweekCus = $customerRepository->createQueryBuilder('customer')
            ->select('count(customer.id) as lastweek')
            ->where('customer.owner = :owner')
            ->andWhere('customer.createdAt >= :lastWeek')
            ->setParameter('owner', $this->getUser())
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
            ->getSingleResult();
        
        $twoweeksCus = $customerRepository->createQueryBuilder('customer')
            ->select('count(customer.id) as twoweeks')
            ->where('customer.owner = :owner')
            ->andWhere('customer.createdAt >= :twoWeeks')
            ->andWhere('customer.createdAt < :lastWeek')
            ->setParameter('owner', $this->getUser())
            ->setParameter('twoWeeks', new \DateTime('-2 week'))
            ->setParameter('lastWeek', new \DateTime('-1 week'))
            ->getQuery()
            ->getSingleResult();

        $customers = [
            'total' => $totalCus,
            'lastweek' => $lastweekCus['lastweek'],
            'twoweeks' => $twoweeksCus['twoweeks'],
        ];
        
        $customers['lastweekPercentage'] = $customers['total'] > 0 ? round(($customers['lastweek'] / $customers['total']) * 100) : 0;

        // dd($products);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'productsWidget' => $products,
            'customersWidget' => $customers
        ]);
    }
}
