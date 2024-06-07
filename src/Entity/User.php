<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ForgotPassword;
use App\Controller\ResetPassword;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['users:read'])]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    private string $firstname;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    private string $lastname;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Groups(['users:read', 'users:write', 'forgotPassword:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['users:write', 'resetPassword:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['users:read'])]
    #[Assert\Length(min: 10, max: 255)]
    private ?string $confirmationToken = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(['users:read'])]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $confirmedAt = null;

    #[ORM\Column(type: "json")]
    #[Groups(['users:read'])]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $phone = null;

    #[ORM\Column(type: "string", length: 40, nullable: true)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 40)]
    private ?string $businessName = null;

    #[ORM\Column(type: "bigint")]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: "/^\d{14}$/", message: 'Le numéro SIRET doit contenir 14 chiffres.')]
    private string $siret;

    #[ORM\Column(type: "string", length: 13, nullable: true)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Regex(
        pattern: "/^([A-Z]{2})(\d{2})(\d{9})$/",
        message: "Le numéro de TVA n'est pas au format valide (FR 00 000000000)."
    )]
    private ?string $tvaNumber = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    private string $address;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    private string $city;

    #[ORM\Column(type: "integer")]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    private int $postalCode;

    #[ORM\Column(type: "string", length: 3)]
    #[Groups(['users:read', 'users:write'])]
    #[Assert\NotBlank]
    #[Assert\Country(alpha3: true)]
    private string $country;

    #[ORM\Column(type: "datetime")]
    #[Groups(['users:read'])]
    #[Assert\Type(DateTimeInterface::class)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Groups(['users:read'])]
    #[Assert\Type(DateTimeInterface::class)]
    private ?DateTimeInterface $updatedAt = null;

    // #[ORM\OneToMany(targetEntity: Customer::class, mappedBy: "owner", orphanRemoval: true, cascade: ["persist"])]
    // private Collection $customers;

    public function __construct()
    {
        $this->setCreatedAt(new DateTime());
        // $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    /**
     * @return Collection|Customer[]
     */
    // public function getCustomers(): Collection
    // {
    //     return $this->customers;
    // }

    // public function addCustomer(Customer $customer): self
    // {
    //     if (!$this->customers->contains($customer)) {
    //         $this->customers[] = $customer;
    //         $customer->setOwner($this);
    //     }

    //     return $this;
    // }

    // public function removeCustomer(Customer $customer): self
    // {
    //     if ($this->customers->removeElement($customer)) {
    //         // set the owning side to null (unless already changed)
    //         if ($customer->getOwner() === $this) {
    //             $customer->setOwner(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getConfirmedAt(): ?DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?DateTimeInterface $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

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

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getTvaNumber(): ?string
    {
        return $this->tvaNumber;
    }

    public function setTvaNumber(?string $tvaNumber): self
    {
        $this->tvaNumber = $tvaNumber;

        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}
