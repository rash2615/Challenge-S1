<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $customer_id = null;

    #[ORM\Column(type: Types::STRING, length: 9)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sent_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paid_at = null;

    #[ORM\Column(type: Types::STRING, length: 13)]
    private ?string $chrono = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $tva_applicable = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $service_done_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $payment_deadline = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $payment_delay_rate = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $is_draft = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    public function setCustomerId(int $customer_id): static
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(?\DateTimeInterface $sent_at): static
    {
        $this->sent_at = $sent_at;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeInterface $paid_at): static
    {
        $this->paid_at = $paid_at;

        return $this;
    }

    public function getChrono(): ?string
    {
        return $this->chrono;
    }

    public function setChrono(string $chrono): static
    {
        $this->chrono = $chrono;

        return $this;
    }

    public function isTvaApplicable(): ?bool
    {
        return $this->tva_applicable;
    }

    public function setTvaApplicable(bool $tva_applicable): static
    {
        $this->tva_applicable = $tva_applicable;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getServiceDoneAt(): ?\DateTimeInterface
    {
        return $this->service_done_at;
    }

    public function setServiceDoneAt(\DateTimeInterface $service_done_at): static
    {
        $this->service_done_at = $service_done_at;

        return $this;
    }

    public function getPaymentDeadline(): ?\DateTimeInterface
    {
        return $this->payment_deadline;
    }

    public function setPaymentDeadline(\DateTimeInterface $payment_deadline): static
    {
        $this->payment_deadline = $payment_deadline;

        return $this;
    }

    public function getPaymentDelayRate(): ?int
    {
        return $this->payment_delay_rate;
    }

    public function setPaymentDelayRate(?int $payment_delay_rate): static
    {
        $this->payment_delay_rate = $payment_delay_rate;

        return $this;
    }

    public function isDraft(): ?bool
    {
        return $this->is_draft;
    }

    public function setDraft(bool $is_draft): static
    {
        $this->is_draft = $is_draft;

        return $this;
    }
}
