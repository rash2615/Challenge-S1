<?php

namespace App\Entity;

// use ApiPlatform\Core\Annotation\ApiFilter;
// use ApiPlatform\Core\Annotation\ApiResource;
// use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
// use App\Controller\CreateUpdateInvoiceDevis;
use App\Repository\DevisRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
// #[
//     ApiResource(
//         collectionOperations: [
//             'get' => [
//                 'normalization_context' => ['groups' => ['allDevis:read']],
//             ],
//             'post' => ['controller' => CreateUpdateInvoiceDevis::class],
//         ],
//         itemOperations: [
//             'get' => ['security' => 'object.getCustomer().getOwner() == user'],
//             'put' => [
//                 'security' => 'object.getCustomer().getOwner() == user',
//                 'denormalization_context' => ['groups' => ['devis:update']],
//                 'controller' => CreateUpdateInvoiceDevis::class,
//             ],
//             'delete' => ['security' => 'object.getCustomer().getOwner() == user'],
//         ],
//         subresourceOperations: [
//             'api_customers_devis_get_subresource' => [
//                 'security' => "is_granted('GET_SUBRESOURCE', _api_normalization_context['subresource_resources'])",
//                 'normalization_context' => ['groups' => ['customers_devis_subresource']],
//             ],
//         ],
//         denormalizationContext: ['groups' => ['devis:write', 'devis:service_write']],
//         normalizationContext: ['groups' => ['devis:read', 'devis:service_read']],
//         paginationClientItemsPerPage: true,
//         paginationMaximumItemsPerPage: 500
//     )
// ]
// #[ApiFilter(OrderFilter::class, properties: ["createdAt" => "desc"], arguments: ["orderParameterName" => "order"])]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups([
        'devis:read',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: "devis")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'allDevis:read'
    ])]
    private ?Customer $customer = null;

    #[ORM\Column(type: "string", length: 13)]
    #[Assert\Regex(
        pattern: "/^D-(\d{4})-(\d{6})$/",
        message: "Le chrono n'est pas au format valide (exemple: D-2021-0001)."
    )]
    #[Groups([
        'devis:read',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private string $chrono;

    #[ORM\Column(type: "string", length: 10)]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['NEW', 'SENT', 'SIGNED', 'CANCELLED'],
        message: "Le statut doit être de type 'NEW', 'SENT', 'SIGNED' ou 'CANCELLED'."
    )]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private string $status;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private DateTimeInterface $validityDate;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(DateTimeInterface::class)]
    #[Groups([
        'devis:read',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private DateTimeInterface $workStartDate;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private string $workDuration;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type(DateTimeInterface::class)]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private DateTimeInterface $paymentDeadline;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Range(
        notInRangeMessage: "Le taux des pénalités de retard ou d'absence de paiement doit être
        un pourcentage compris entre {{ min }} et {{ max }}.",
        min: 0,
        max: 100
    )]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private ?int $paymentDelayRate = null;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private bool $tvaApplicable;

    #[ORM\Column(type: "boolean")]
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private bool $isDraft;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    #[Groups([
        'devis:read',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private ?DateTimeInterface $sentAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    #[Groups([
        'devis:read',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private ?DateTimeInterface $signedAt = null;

    #[ORM\OneToMany(
        mappedBy: "devis",
        targetEntity: InvoiceService::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true
    )]
    #[Groups([
        'devis:read',
        'devis:write',
        'devis:update',
        'customers_devis_subresource',
        'allDevis:read'
    ])]
    private Collection $services;

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
