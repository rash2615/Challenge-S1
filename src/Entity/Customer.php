<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
// use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: "customers")]
#[ORM\HasLifecycleCallbacks]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 7)]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ['PERSON', 'COMPANY'],
        message: "Le client ne peut-être qu'un particulier (PERSON) ou une entreprise (COMPANY)."
    )]
    private string $type;

    #[ORM\Column(type: "string", length: 30, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(min: 2, max: 30)]
    private ?string $firstname = null;

    #[ORM\Column(type: "string", length: 30, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(min: 2, max: 30)]
    private ?string $lastname = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: "string", length: 30, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $phone = null;

    #[ORM\Column(type: "string", length: 40, nullable: true)]
    // #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 40)]
    private ?string $company = null;

    #[ORM\Column(type: "bigint", nullable: true)]
    // #[Assert\NotBlank(allowNull: true)]
    #[Assert\Regex(pattern: "/^\d{14}$/", message: 'Le numéro SIRET doit contenir 14 chiffres.')]
    private ?string $siret = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $address;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank]
    private int $postalCode;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $city;

    #[ORM\Column(type: "string", length: 3)]
    #[Assert\NotBlank]
    #[Assert\Country(alpha3: true, message: "Le code pays n'est pas valide. ex: FRA pour la France.")]
    private string $country;

    // #[Vich\UploadableField(mapping: "customer_picture", fileNameProperty: 'picture')]
    #[Assert\File(maxSize: "5M", mimeTypes: ["image/png", "image/jpeg"])]
    private ?File $pictureFile = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $picture = null;

    private ?string $pictureUrl = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "customers")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?User $owner = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: "customer", orphanRemoval: true, cascade: ["persist"])]
    private Collection $invoices;

    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: "customer", orphanRemoval: true, cascade: ["persist"])]
    private Collection $devis;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->invoices = new ArrayCollection();
        $this->devis = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /* Returns the last Invoice of the Customer for the UI */
    // #[Groups(['users_customers_subresource'])]
    public function getLastInvoice()
    {
        if (!$this->invoices->isEmpty()) {
            return $this->invoices->last();
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

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

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(?File $pictureFile): self
    {
        $this->pictureFile = $pictureFile;

        if ($pictureFile) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl(?string $pictureUrl): self
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @return Collection|Devis[]
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): self
    {
        if (!$this->devis->contains($devi)) {
            $this->devis[] = $devi;
            $devi->setCustomer($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getCustomer() === $this) {
                $devi->setCustomer(null);
            }
        }

        return $this;
    }
}
