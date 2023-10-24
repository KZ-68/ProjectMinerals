<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'image:item:read']),
        new GetCollection(normalizationContext: ['groups' => 'image:list:read'])
    ],
    order: ['name' => 'DESC'],
    paginationEnabled: false,
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['image:item:read', 'image:list:read', 'mineral:item:read', 'variety:item:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['image:item:read', 'image:list:read', 'mineral:item:read', 'variety:item:read'])]
    private ?string $filename = null;

    #[ORM\Column]
    #[Groups(['image:item:read', 'image:list:read', 'mineral:item:read', 'variety:item:read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'd-m-Y'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image:item:read'])]
    private ?Mineral $mineral = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image:item:read'])]
    private ?Variety $variety = null;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['image:list:read', 'image:item:read', 'mineral:item:read', 'variety:item:read'])]
    private ?string $slug = null;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
        $this->modificationHistories = new ArrayCollection();

    }

    #[ORM\PrePersist]
    public function prePersist() {
        $this->slug = (new Slugify())->slugify($this->filename);
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
