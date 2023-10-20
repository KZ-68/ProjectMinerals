<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'image:item']),
        new GetCollection(normalizationContext: ['groups' => 'image:list'])
    ],
    order: ['name' => 'DESC'],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['image:item', 'image:list', 'mineral:item', 'variety:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['image:item', 'image:list', 'mineral:item', 'variety:item'])]
    private ?string $filename = null;

    #[ORM\Column]
    #[Groups(['image:item', 'image:list', 'mineral:item', 'variety:item'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image:item'])]
    private ?Mineral $mineral = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image:item'])]
    private ?Variety $variety = null;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
        $this->modificationHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
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

    public function getMineral(): ?Mineral
    {
        return $this->mineral;
    }

    public function setMineral(?Mineral $mineral): static
    {
        $this->mineral = $mineral;

        return $this;
    }

    public function getVariety(): ?Variety
    {
        return $this->variety;
    }

    public function setVariety(?Variety $variety): static
    {
        $this->variety = $variety;

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
            $modificationHistory->setImage($this);
        }

        return $this;
    }

    public function removeModificationHistory(ModificationHistory $modificationHistory): static
    {
        if ($this->modificationHistories->removeElement($modificationHistory)) {
            // set the owning side to null (unless already changed)
            if ($modificationHistory->getImage() === $this) {
                $modificationHistory->setImage(null);
            }
        }

        return $this;
    }
}
