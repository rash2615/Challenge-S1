<?php

namespace App\Entity;

use App\Repository\ResetPasswordRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ResetPasswordRepository::class)]
#[ORM\Table(name: "users_reset_password")]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['token'], message: 'Token invalide.')]
class ResetPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 255)]
    private ?string $token = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?DateTimeImmutable $requestedAt = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?DateTimeImmutable $expiresAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getRequestedAt(): ?DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(DateTimeImmutable $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersistValues()
    {
        $this->setToken(sha1(random_bytes(rand(8, 10))));
        $this->setRequestedAt(new DateTimeImmutable());
        $this->setExpiresAt($this->getRequestedAt()->add(new DateInterval('PT2H')));
    }
}
