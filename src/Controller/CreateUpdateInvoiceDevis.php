<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invoices/create', name: 'api_invoices')]
class CreateUpdateInvoiceDevis
{
    public function __construct(private Security $security, private CustomerRepository $customerRepository)
    {
    }

    /**
     * Check that the User did not try to set an Invoice or a Devis for a Customer he didn't own.
     */
    public function __invoke(Invoice|Devis $data): Invoice|Devis|\Exception
    {
        $user = $this->security->getUser();
        $userCustomers = $this->customerRepository->findBy(['owner' => $user]);

        if (is_null($data->getCustomer())) {
            throw new BadRequestException("Un client doit être passé comme paramètre dans le requête POST.");
        }
        if (!in_array($data->getCustomer(), $userCustomers)) {
            throw new AccessDeniedException(
                "Vous ne pouvez pas assigner une facture à un client que vous ne possédez pas."
            );
        }

        return $data;
    }
}
