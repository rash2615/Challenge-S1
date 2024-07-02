<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: "invoices")]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: "invoices")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Customer $customer = null;

    #[ORM\Column(type: "string", length: 13)]
    #[Assert\Regex(
        pattern: "/^F-(\d{4})-(\d{6})$/",
        message: "Le chrono n'est pas au format valide (exemple: F-2021-0001)."
    )]
    private string $chrono;

    #[ORM\Column(type: "string", length: 9)]
    // #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['NEW', 'SENT', 'PAID', 'CANCELLED'],
        message: "Le statut doit être de type 'NEW', 'SENT', 'PAID' ou 'CANCELLED'."
    )]
    private string $status;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    private bool $tvaApplicable;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $serviceDoneAt;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $paymentDeadline;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "Le taux des péanlités de retard ou d'absence de paiement doit être
        un pourcentage compris entre {{ min }} et {{ max }}."
    )]
    private ?int $paymentDelayRate = null;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    private bool $isDraft;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $sentAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $paidAt = null;

    #[ORM\OneToMany(
        targetEntity: InvoiceService::class,
        mappedBy: "invoice",
        orphanRemoval: true,
        cascade: ["persist", "remove"]
    )]
    private Collection $services;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable:true)]
    private $users;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->services = new ArrayCollection();
    }

    /* Returns the total amount of all the services prices */
    public function getTotalAmount()
    {
        $totalAmount = 0;

        foreach ($this->services as $service) {
            $totalAmount += $service->getUnitPrice() * $service->getQuantity();
        }

        return $totalAmount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getPaidAt(): ?DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(?DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getChrono(): ?string
    {
        return $this->chrono;
    }

    public function setChrono(string $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTvaApplicable(): ?bool
    {
        return $this->tvaApplicable;
    }

    public function setTvaApplicable(bool $tvaApplicable): self
    {
        $this->tvaApplicable = $tvaApplicable;

        return $this;
    }

    public function getPaymentDeadline(): ?DateTimeInterface
    {
        return $this->paymentDeadline;
    }

    public function setPaymentDeadline(DateTimeInterface $paymentDeadline): self
    {
        $this->paymentDeadline = $paymentDeadline;

        return $this;
    }

    public function getPaymentDelayRate(): ?int
    {
        return $this->paymentDelayRate;
    }

    public function setPaymentDelayRate(?int $paymentDelayRate): self
    {
        $this->paymentDelayRate = $paymentDelayRate;

        return $this;
    }

    public function getIsDraft(): ?bool
    {
        return $this->isDraft;
    }

    public function setIsDraft(bool $isDraft): self
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    public function getServiceDoneAt(): ?DateTimeInterface
    {
        return $this->serviceDoneAt;
    }

    public function setServiceDoneAt(DateTimeInterface $serviceDoneAt): self
    {
        $this->serviceDoneAt = $serviceDoneAt;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection|InvoiceService[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(InvoiceService $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setInvoice($this);
        }

        return $this;
    }

    public function removeService(InvoiceService $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getInvoice() === $this) {
                $service->setInvoice(null);
            }
        }

        return $this;
    }
}
