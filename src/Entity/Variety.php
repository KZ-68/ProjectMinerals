<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\VarietyRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'variety:item:read']),
        new GetCollection(normalizationContext: ['groups' => 'variety:list:read'])
    ],
    order: ['name' => 'DESC'],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: VarietyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Variety
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['variety:item:read', 'variety:list:read', 'mineral:item:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'The name {{ value }} contains non valid caracters'
    )]
    #[Groups(['variety:item:read', 'variety:list:read', 'mineral:item:read'])]
    #[ApiProperty(types: ['https://schema.org/name'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'varieties')]
    #[Groups(['variety:item:read'])]
    private ?Mineral $mineral = null;

    #[ORM\OneToMany(mappedBy: 'variety', cascade: ['persist'], orphanRemoval: true, targetEntity: Image::class)]
    #[Groups(['variety:item:read'])]
    private Collection $images;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['variety:item:read', 'variety:list:read', 'mineral:item:read'])]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Coordinate::class, inversedBy: 'varieties')]
    #[Groups(['variety:item:read', 'mineral:item:read'])]
    private Collection $coordinates;

    #[ORM\OneToMany(mappedBy: 'variety', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->coordinates = new ArrayCollection();
        $this->modificationHistories = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist() {
        $this->slug = (new Slugify())->slugify($this->name);
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

    public function getMineral(): ?Mineral
    {
        return $this->mineral;
    }

    public function setMineral(?Mineral $mineral): static
    {
        $this->mineral = $mineral;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setVariety($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getVariety() === $this) {
                $image->setVariety(null);
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

    /**
     * @return Collection<int, Coordinate>
     */
    public function getCoordinates(): Collection
    {
        return $this->coordinates;
    }

    public function addCoordinate(Coordinate $coordinate): static
    {
        if (!$this->coordinates->contains($coordinate)) {
            $this->coordinates->add($coordinate);
        }

        return $this;
    }

    public function removeCoordinate(Coordinate $coordinate): static
    {
        $this->coordinates->removeElement($coordinate);

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
            $modificationHistory->setVariety($this);
        }

        return $this;
    }

    public function removeModificationHistory(ModificationHistory $modificationHistory): static
    {
        if ($this->modificationHistories->removeElement($modificationHistory)) {
            // set the owning side to null (unless already changed)
            if ($modificationHistory->getVariety() === $this) {
                $modificationHistory->setVariety(null);
            }
        }

        return $this;
    }
}
