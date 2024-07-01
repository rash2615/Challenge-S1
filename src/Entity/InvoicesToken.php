<?php

namespace App\Entity;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Repository\InvoicesTokenRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoicesTokenRepository::class)]
#[ORM\Table(name: "invoices_token")]
#[ORM\HasLifecycleCallbacks]
class InvoicesToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "invoicesTokens")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: "invoicesTokens")]
    #[ORM\JoinColumn(name: "invoice_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Invoice $invoice;

    #[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: "invoicesTokens")]
    #[ORM\JoinColumn(name: "devis_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Devis $devis;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $token;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(DateTimeInterface::class)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(DateTimeInterface::class)]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getInvoices(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoices(?Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): void
    {
        $this->devis = $devis;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}