<?php

namespace App\Entity;

use App\Repository\InvoiceServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceServiceRepository::class)]
#[ORM\Table(name: "invoices_services")]
class InvoiceService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "product", cascade: ["persist"])]
    private ?Product $product = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?int $quantity = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank]
    private float $unitPrice;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: "services")]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: "services")]
    private ?Devis $devis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): self
    {
        $this->devis = $devis;

        return $this;
    }
}
