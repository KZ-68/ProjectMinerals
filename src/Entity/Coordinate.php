<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CoordinateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource()]
#[ORM\Entity(repositoryClass: CoordinateRepository::class)]
class Coordinate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column(length: 50)]
    private ?string $latitude = null;

    #[ORM\Column(length: 50)]
    private ?string $longitude = null;

    #[ORM\ManyToMany(targetEntity: Mineral::class, mappedBy: 'coordinates')]
    private Collection $minerals;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
        $this->minerals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, Mineral>
     */
    public function getMinerals(): Collection
    {
        return $this->minerals;
    }

    public function addMineral(Mineral $mineral): static
    {
        if (!$this->minerals->contains($mineral)) {
            $this->minerals->add($mineral);
            $mineral->addCoordinate($this);
        }

        return $this;
    }

    public function removeMineral(Mineral $mineral): static
    {
        if ($this->minerals->removeElement($mineral)) {
            $mineral->removeCoordinate($this);
        }

        return $this;
    }
}
