<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LustreRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'lustre:item']),
        new GetCollection(normalizationContext: ['groups' => 'lustre:list'])
    ],
    order: ['type' => 'DESC'],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: LustreRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Lustre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['lustre:item', 'lustre:list', 'mineral:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'The type {{ value }} contains non valid caracters'
    )]
    #[Groups(['lustre:item', 'lustre:list', 'mineral:item'])]
    private ?string $type = null;

    #[ORM\ManyToMany(targetEntity: Mineral::class, inversedBy: 'lustres')]
    #[Groups(['lustre:item'])]
    private Collection $minerals;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['lustre:item', 'lustre:list', 'mineral:item'])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'lustre', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    public function __construct()
    {
        $this->minerals = new ArrayCollection();
        $this->modificationHistories = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist() {
        $this->slug = (new Slugify())->slugify($this->type);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
            $modificationHistory->setLustre($this);
        }

        return $this;
    }

    public function removeModificationHistory(ModificationHistory $modificationHistory): static
    {
        if ($this->modificationHistories->removeElement($modificationHistory)) {
            // set the owning side to null (unless already changed)
            if ($modificationHistory->getLustre() === $this) {
                $modificationHistory->setLustre(null);
            }
        }

        return $this;
    }
}
