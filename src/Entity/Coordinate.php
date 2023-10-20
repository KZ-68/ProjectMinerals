<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CoordinateRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'coordinate:item']),
        new GetCollection(normalizationContext: ['groups' => 'coordinate:list'])
    ],
    order: ['latitude' => 'DESC'],
    paginationEnabled: false,

)]
#[ORM\Entity(repositoryClass: CoordinateRepository::class)]
class Coordinate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['coordinate:item', 'coordinate:list', 'mineral:item', 'variety:item'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['coordinate:item', 'coordinate:list', 'mineral:item', 'variety:item'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(['coordinate:item', 'mineral:item', 'variety:item'])]
    private ?string $latitude = null;

    #[ORM\Column(length: 50)]
    #[Groups(['coordinate:item', 'mineral:item', 'variety:item'])]
    private ?string $longitude = null;

    #[ORM\ManyToMany(targetEntity: Mineral::class, mappedBy: 'coordinates')]
    #[Groups(['coordinate:item'])]
    private Collection $minerals;

    #[ORM\ManyToMany(targetEntity: Variety::class, mappedBy: 'coordinates')]
    #[Groups(['coordinate:item'])]
    private Collection $varieties;

    #[ORM\OneToMany(mappedBy: 'coordinate', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
        $this->minerals = new ArrayCollection();
        $this->varieties = new ArrayCollection();
        $this->modificationHistories = new ArrayCollection();
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

    /**
     * @return Collection<int, Variety>
     */
    public function getVarieties(): Collection
    {
        return $this->varieties;
    }

    public function addVariety(Variety $variety): static
    {
        if (!$this->varieties->contains($variety)) {
            $this->varieties->add($variety);
            $variety->addCoordinate($this);
        }

        return $this;
    }

    public function removeVariety(Variety $variety): static
    {
        if ($this->varieties->removeElement($variety)) {
            $variety->removeCoordinate($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ModificationHistory>
     */
    public function getModificationHistories(): Collection
    {
        return $this->modificationHistories;
    }

    public function addModificationHistory(ModificationHistory $modificationHistory): static
    {
        if (!$this->modificationHistories->contains($modificationHistory)) {
            $this->modificationHistories->add($modificationHistory);
            $modificationHistory->setCoordinate($this);
        }

        return $this;
    }

    public function removeModificationHistory(ModificationHistory $modificationHistory): static
    {
        if ($this->modificationHistories->removeElement($modificationHistory)) {
            // set the owning side to null (unless already changed)
            if ($modificationHistory->getCoordinate() === $this) {
                $modificationHistory->setCoordinate(null);
            }
        }

        return $this;
    }
}
