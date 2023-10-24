<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ColorRepository;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'color:item:read']),
        new GetCollection(normalizationContext: ['groups' => 'color:list:read'])
    ],
    order: ['name' => 'DESC'],
    paginationEnabled: false,

)]
#[ORM\Entity(repositoryClass: ColorRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Color
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['color:list:read', 'color:item:read', 'mineral:item:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'The name {{ value }} contains non valid caracters'
    )]
    #[Groups(['color:list:read', 'color:item:read', 'mineral:item:read'])]
    #[ApiProperty(types: ['https://schema.org/name'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Mineral::class, inversedBy: 'colors')]
    #[Groups(['color:item:read'])]
    private Collection $minerals;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['color:list:read', 'color:item:read', 'mineral:item:read'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'color', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    public function __construct()
    {
        $this->minerals = new ArrayCollection();
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
        }

        return $this;
    }

    public function removeMineral(Mineral $mineral): static
    {
        $this->minerals->removeElement($mineral);

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
            $modificationHistory->setColor($this);
        }

        return $this;
    }

    public function removeModificationHistory(ModificationHistory $modificationHistory): static
    {
        if ($this->modificationHistories->removeElement($modificationHistory)) {
            // set the owning side to null (unless already changed)
            if ($modificationHistory->getColor() === $this) {
                $modificationHistory->setColor(null);
            }
        }

        return $this;
    }
}
