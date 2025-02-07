<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $province = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $area = null;

    /**
     * @var Collection<int, Stage>
     */
    #[ORM\ManyToMany(targetEntity: Stage::class, inversedBy: 'schools')]
    private Collection $stage;

    /**
     * @var Collection<int, Insured>
     */
    #[ORM\OneToMany(targetEntity: Insured::class, mappedBy: 'school')]
    private Collection $insureds;

    public function __construct()
    {
        $this->stage = new ArrayCollection();
        $this->insureds = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(string $area): static
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStage(): Collection
    {
        return $this->stage;
    }

    public function addStage(Stage $stage): static
    {
        if (!$this->stage->contains($stage)) {
            $this->stage->add($stage);
        }

        return $this;
    }

    public function removeStage(Stage $stage): static
    {
        $this->stage->removeElement($stage);

        return $this;
    }

    /**
     * @return Collection<int, Insured>
     */
    public function getInsureds(): Collection
    {
        return $this->insureds;
    }

    public function addInsured(Insured $insured): static
    {
        if (!$this->insureds->contains($insured)) {
            $this->insureds->add($insured);
            $insured->setSchool($this);
        }

        return $this;
    }

    public function removeInsured(Insured $insured): static
    {
        if ($this->insureds->removeElement($insured)) {
            // set the owning side to null (unless already changed)
            if ($insured->getSchool() === $this) {
                $insured->setSchool(null);
            }
        }

        return $this;
    }
}
