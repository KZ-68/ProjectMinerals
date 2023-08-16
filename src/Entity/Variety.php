<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VarietyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource()]
#[ORM\Entity(repositoryClass: VarietyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Variety
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'The name {{ value }} contains non valid caracters'
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'varieties')]
    private ?Mineral $mineral = null;

    #[ORM\OneToMany(mappedBy: 'variety', cascade: ['persist'], orphanRemoval: true, targetEntity: Image::class)]
    private Collection $images;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
}
