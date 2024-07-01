<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\InvoiceService;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\PDFGeneratorService;

#[Route('/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(Request $request, DevisRepository $devisRepository): Response
    {
        $listDevis = $devisRepository->findByPage(
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('dashboard/devis.html.twig', [
            'devis' => $listDevis["devis"],
            'total' => $listDevis["total"],
            'page' => $listDevis["page"],
            'pages' => $listDevis["pages"],
            'get' => 'page',
            'limit' => $listDevis["limit"]
        ]);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CustomerRepository $customerRepository, ProductRepository $productRepository): Response
    {
        $getAllUsers = $customerRepository->createQueryBuilder('customers')
            ->where('customers.owner = :owner')
            ->setParameter('owner', $this->getUser())
            ->getQuery()
            ->getResult();

        $getAllProduct = $productRepository->createQueryBuilder('product')
            ->select("product.id as id, CONCAT(product.id, ' - ', product.nomProduit) as nomProduit")
            ->where('product.users = :users')
            ->setParameter('users', $this->getUser())
            ->getQuery()
            ->getResult();

        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis, [
            'list_users' => $getAllUsers
        ]);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $devis->setStatus('NEW');
            $devis->setChrono('D-' . date('Y') . '-' . substr(hash('crc32', date('Y-m-d H:i:s')), 0, 6));

            $entityManager->persist($devis);

            foreach ($request->get('_product') as $key => $value) {
                $is = new InvoiceService();

                $is->setProduct($productRepository->find($value));
                $is->setQuantity($request->get('_quantity')[$key]);
                $is->setUnitPrice($request->get('_price')[$key]);
                $is->setDevis($devis);

                $entityManager->persist($is);
            }

            // dd(
            //     $entityManager,
            //     // $request
            // );
            
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/devis/new.html.twig', [
            'devis' => $devis,
            'form' => $form,

            'list_product' => $getAllProduct
        ]);
    }

    #[Route('/{id}/view', name: 'app_devis_view', methods: ['GET'])]
    public function edit(Devis $devis): Response
    {
        return $this->render('dashboard/devis/view.html.twig', [
            'devis' => $devis
        ]);
    }

    #[Route('/{id}/edit/statut', name: 'app_devis_edit_statut', methods: ['POST'])]
    public function toEdit(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {
        $mapStatus = [
            0 => 'NEW',
            1 => 'SENT',
            2 => 'SIGNED',
            3 => 'CANCELLED'
        ];

        $statut = $request->getPayload()->get('statut');
        $sentAt = $request->getPayload()->get('sentAt');
        $signedAt = $request->getPayload()->get('signedAt');

        if (!array_key_exists($statut, $mapStatus)) {
            return $this->redirectToRoute('app_devis_view', ['id' => $devis->getId()]);
        }

        if (($statut == 1 && empty($sentAt)) || ($statut == 2 && empty($signedAt))) {
            return $this->redirectToRoute('app_devis_view', ['id' => $devis->getId()]);
        }

        $devis->setStatus($mapStatus[$request->getPayload()->get('statut')]);
        
        if ($statut == 1) {
            $devis->setSentAt(new \DateTime($sentAt));
        } elseif ($statut == 2) {
            $devis->setSignedAt(new \DateTime($signedAt));
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_devis_view', ['id' => $devis->getId()]);
    }

    #[Route('/{id}/delete', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devi->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_devis_pdf', methods: ['GET'])]
    public function pdf(Devis $devis, PDFGeneratorService $pdfGenerator): Response
    {
        $html = $this->renderView('dashboard/devis/pdf.html.twig', [
            'devis' => $devis
        ]);

        $pdf = $pdfGenerator->output($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="devis-"' . $devis->getChrono() . '".pdf"'
        ]);
    }
}
