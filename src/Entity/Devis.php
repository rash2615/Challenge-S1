<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\DevisRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: "devis")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Customer $customer = null;

    #[ORM\Column(type: "string", length: 13)]
    #[Assert\Regex(
        pattern: "/^D-(\d{4})-(\d{6})$/",
        message: "Le chrono n'est pas au format valide (exemple: D-2021-0001)."
    )]
    private string $chrono;

    #[ORM\Column(type: "string", length: 10)]
    #[Assert\Choice(
        choices: ['NEW', 'SENT', 'SIGNED', 'CANCELLED'],
        message: "Le statut doit être de type 'NEW', 'SENT', 'SIGNED' ou 'CANCELLED'."
    )]
    private string $status;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $validityDate;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $workStartDate;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank]
    private string $workDuration;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $paymentDeadline;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Range(
        notInRangeMessage: "Le taux des pénalités de retard ou d'absence de paiement doit être
        un pourcentage compris entre {{ min }} et {{ max }}.",
        min: 0,
        max: 100
    )]
    private ?int $paymentDelayRate = null;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    private bool $tvaApplicable;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    private bool $isDraft;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $sentAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $signedAt = null;

    #[ORM\OneToMany(
        mappedBy: "devis",
        targetEntity: InvoiceService::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChrono(): ?string
    {
        return $this->chrono;
    }

    public function setChrono(string $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
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

    public function getValidityDate(): ?DateTimeInterface
    {
        return $this->validityDate;
    }

    public function setValidityDate(DateTimeInterface $validityDate): self
    {
        $this->validityDate = $validityDate;

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

    public function getWorkStartDate(): ?DateTimeInterface
    {
        return $this->workStartDate;
    }

    public function setWorkStartDate(DateTimeInterface $workStartDate): self
    {
        $this->workStartDate = $workStartDate;

        return $this;
    }

    public function getWorkDuration(): ?string
    {
        return $this->workDuration;
    }

    public function setWorkDuration(string $workDuration): self
    {
        $this->workDuration = $workDuration;

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

    public function getTvaApplicable(): ?bool
    {
        return $this->tvaApplicable;
    }

    public function setTvaApplicable(bool $tvaApplicable): self
    {
        $this->tvaApplicable = $tvaApplicable;

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

    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getSignedAt(): ?DateTimeInterface
    {
        return $this->signedAt;
    }

    public function setSignedAt(?DateTimeInterface $signedAt): self
    {
        $this->signedAt = $signedAt;

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
            $service->setDevis($this);
        }

        return $this;
    }

    public function removeService(InvoiceService $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getDevis() === $this) {
                $service->setDevis(null);
            }
        }

        return $this;
    }
}
