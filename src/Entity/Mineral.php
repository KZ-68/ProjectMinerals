<?php

namespace App\Entity;

use App\Repository\MineralRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MineralRepository::class)]
class Mineral
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $formula = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $crystal_system = null;

    #[ORM\Column(nullable: true)]
    private ?float $density = null;

    #[ORM\Column(nullable: true)]
    private ?int $hardness = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fracture = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $streak = null;

    #[ORM\ManyToOne(inversedBy: 'minerals')]
    private ?Category $category = null;

    #[ORM\ManyToMany(targetEntity: Color::class, mappedBy: 'minerals')]
    private Collection $colors;

    #[ORM\ManyToMany(targetEntity: Lustre::class, mappedBy: 'minerals')]
    private Collection $lustres;

    #[ORM\OneToMany(mappedBy: 'mineral', targetEntity: Variety::class)]
    private Collection $varieties;

    #[ORM\OneToMany(mappedBy: 'mineral', targetEntity: Image::class)]
    private Collection $images;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->lustres = new ArrayCollection();
        $this->varieties = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
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

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(?string $formula): static
    {
        $this->formula = $formula;

        return $this;
    }

    public function getCrystalSystem(): ?string
    {
        return $this->crystal_system;
    }

    public function setCrystalSystem(?string $crystal_system): static
    {
        $this->crystal_system = $crystal_system;

        return $this;
    }

    public function getDensity(): ?float
    {
        return $this->density;
    }

    public function setDensity(?float $density): static
    {
        $this->density = $density;

        return $this;
    }

    public function getHardness(): ?int
    {
        return $this->hardness;
    }

    public function setHardness(?int $hardness): static
    {
        $this->hardness = $hardness;

        return $this;
    }

    public function getFracture(): ?string
    {
        return $this->fracture;
    }

    public function setFracture(?string $fracture): static
    {
        $this->fracture = $fracture;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStreak(): ?string
    {
        return $this->streak;
    }

    public function setStreak(?string $streak): static
    {
        $this->streak = $streak;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Color>
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(Color $color): static
    {
        if (!$this->colors->contains($color)) {
            $this->colors->add($color);
            $color->addMineral($this);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        if ($this->colors->removeElement($color)) {
            $color->removeMineral($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Lustre>
     */
    public function getLustres(): Collection
    {
        return $this->lustres;
    }

    public function addLustre(Lustre $lustre): static
    {
        if (!$this->lustres->contains($lustre)) {
            $this->lustres->add($lustre);
            $lustre->addMineral($this);
        }

        return $this;
    }

    public function removeLustre(Lustre $lustre): static
    {
        if ($this->lustres->removeElement($lustre)) {
            $lustre->removeMineral($this);
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
            $variety->setMineral($this);
        }

        return $this;
    }

    public function removeVariety(Variety $variety): static
    {
        if ($this->varieties->removeElement($variety)) {
            // set the owning side to null (unless already changed)
            if ($variety->getMineral() === $this) {
                $variety->setMineral(null);
            }
        }

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
            $image->setMineral($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getMineral() === $this) {
                $image->setMineral(null);
            }
        }

        return $this;
    }
}
