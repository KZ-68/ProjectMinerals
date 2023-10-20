<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'category:item']),
        new GetCollection(normalizationContext: ['groups' => 'category:list'])
    ],
    order: ['name' => 'DESC'],
    paginationEnabled: false,

)]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:item', 'category:list', 'mineral:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'The name {{ value }} contains non valid caracters'
    )]
    #[Groups(['category:item', 'category:list', 'mineral:item'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Mineral::class)]
    #[Groups(['category:item'])]
    private Collection $minerals;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['category:item', 'category:list', 'mineral:item'])]
    private ?string $slug = null;

    public function __construct()
    {
        $this->minerals = new ArrayCollection();
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
            $mineral->setCategory($this);
        }

        return $this;
    }

    public function removeMineral(Mineral $mineral): static
    {
        if ($this->minerals->removeElement($mineral)) {
            // set the owning side to null (unless already changed)
            if ($mineral->getCategory() === $this) {
                $mineral->setCategory(null);
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
