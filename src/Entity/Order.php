<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $applicant = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Insured $insured = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Product $product = null;

    #[ORM\Column(length: 255)]
    private ?string $sn = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = 0;

    #[ORM\Column(nullable: true)]
    private ?int $amount = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        // $this->updatedAt = new \DateTimeImmutable();
        $this->sn = strtoupper(str_replace('.', '', uniqid('', true)));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): static
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getInsured(): ?Insured
    {
        return $this->insured;
    }

    public function setInsured(?Insured $insured): static
    {
        $this->insured = $insured;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}
