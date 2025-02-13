<?php

namespace App\Entity;

use App\Repository\RefundRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefundRepository::class)]
class Refund
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sn = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $ord = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $refundedAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = 0;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->sn = 'R' . strtoupper(str_replace('.', '', uniqid('', true)));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): static
    {
        $this->sn = $sn;

        return $this;
    }

    public function getOrd(): ?Order
    {
        return $this->ord;
    }

    public function setOrd(?Order $ord): static
    {
        $this->ord = $ord;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRefundedAt(): ?\DateTimeImmutable
    {
        return $this->refundedAt;
    }

    public function setRefundedAt(?\DateTimeImmutable $refundedAt): static
    {
        $this->refundedAt = $refundedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }
}
