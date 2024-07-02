<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceService;
use App\Entity\InvoicesToken;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\InvoicesTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\PDFGeneratorService;
use App\Service\SendMailService;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository): Response
    {
        $listInvoice = $invoiceRepository->findByPage(
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('dashboard/invoice.html.twig', [
            'invoices' => $listInvoice["invoices"],
            'total' => $listInvoice["total"],
            'page' => $listInvoice["page"],
            'pages' => $listInvoice["pages"],
            'get' => 'page',
            'limit' => $listInvoice["limit"],
        ]);
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
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

        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice, [
            'list_users' => $getAllUsers
        ]);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $invoice->setStatus('NEW');
            $invoice->setChrono('F-' . date('Y') . '-' . substr(hash('crc32', date('Y-m-d H:i:s')), 0, 6));
            $invoice->setServiceDoneAt(new \DateTime());
            $invoice->setUsers($this->getUser());

            // dd($invoice);
            $entityManager->persist($invoice);

            foreach ($request->get('_product') as $key => $value) {
                $is = new InvoiceService();

                $is->setProduct($productRepository->find($value));
                $is->setQuantity($request->get('_quantity')[$key]);
                $is->setUnitPrice($request->get('_price')[$key]);
                $is->setInvoice($invoice);

                $entityManager->persist($is);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,

            'list_product' => $getAllProduct,
        ]);
    }

    #[Route('/{id}/view', name: 'app_invoice_view', methods: ['GET'])]
    public function edit(Invoice $invoice): Response
    {
        return $this->render('dashboard/invoice/view.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit/statut', name: 'app_invoice_edit_statut', methods: ['POST'])]
    public function toEdit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $mapStatus = [
            0 => 'NEW',
            1 => 'SENT',
            2 => 'PAID',
            3 => 'CANCELLED'
        ];

        $statut = $request->getPayload()->get('statut');
        $sentAt = $request->getPayload()->get('sentAt');
        $paidAt = $request->getPayload()->get('paidAt');

        if (!array_key_exists($statut, $mapStatus)) {
            return $this->redirectToRoute('app_invoice_view', ['id' => $invoice->getId()]);
        }

        if (($statut == 1 && empty($sentAt)) || ($statut == 2 && empty($paidAt))) {
            return $this->redirectToRoute('app_invoice_view', ['id' => $invoice->getId()]);
        }

        $invoice->setStatus($mapStatus[$request->getPayload()->get('statut')]);
        
        if ($statut == 1) {
            $invoice->setSentAt(new \DateTime($sentAt));
        } elseif ($statut == 2) {
            $invoice->setPaidAt(new \DateTime($paidAt));
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_invoice_view', ['id' => $invoice->getId()]);
    }

    #[Route('/{id}/delete', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_invoice_pdf', methods: ['GET'])]
    public function pdf(Invoice $invoice, PDFGeneratorService $pdfGenerator): Response
    {
        $html = $this->renderView('dashboard/invoice/pdf.html.twig', [
            'invoice' => $invoice
        ]);

        $pdf = $pdfGenerator->output($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-"' . $invoice->getChrono() . '".pdf"'
        ]);
    }

    #[Route('/{id}/pdf/show', name: 'app_invoice_pdf_show', methods: ['GET'])]
    public function show(Request $request, Invoice $invoice, PDFGeneratorService $pdfGenerator, InvoicesTokenRepository $invoicesTokenRepository): Response
    {
        // vérifier le token pour accedeer a la facture
        $token = $request->query->get('token');

        $access = $invoicesTokenRepository->createQueryBuilder('it')
                ->where('it.token = :token')
                ->andWhere('it.invoice = :invoice')
                ->setParameter('token', $token)
                ->setParameter('invoice', $invoice)
                ->getQuery()
                ->getOneOrNullResult();

        if (!$access) {
            return $this->redirectToRoute('app_home');
        }

        $html = $this->renderView('dashboard/invoice/pdf.html.twig', [
            'invoice' => $invoice
        ]);

        $pdf = $pdfGenerator->output($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="facture-"' . $invoice->getChrono() . '".pdf"'
        ]);
    }

    #[Route('/{id}/pdf/mail', name: 'app_invoice_pdf_mail', methods: ['GET'])]
    public function mail(Invoice $invoice, SendMailService $mail, EntityManagerInterface $entityManager): Response
    {
        // create new token (max: 50 characters) for the devis
        $tokenHash = bin2hex(random_bytes(25));

        $invoicesToken = new InvoicesToken();
        $invoicesToken->setUser($this->getUser());
        $invoicesToken->setInvoices($invoice);
        $invoicesToken->setToken($tokenHash);

        $entityManager->persist($invoicesToken);
        $entityManager->flush();

        // envoyer un mail
        $mail->send(
            "no-reply@luxar.space",
            $invoice->getCustomer()->getEmail(),
            'Devis ' . $invoice->getChrono(),
            'dashboard/emails/invoice.html.twig',
            [
                'invoice' => $invoice,
                'token' => $tokenHash
            ]
        );

        return $this->redirectToRoute('app_invoice_view', ['id' => $invoice->getId()]);
    }
}
