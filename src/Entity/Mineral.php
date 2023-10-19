<?php

namespace App\Entity;

use App\Entity\Coordinate;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\MineralRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource()]
#[ORM\Entity(repositoryClass: MineralRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Mineral
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
    #[Assert\NotBlank(
        message: 'Le nom ne peut pas être laissée vide'
    )]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'The name {{ value }} contains non valid caracters'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    private ?string $formula = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    private ?string $crystal_system = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2, nullable: true)]
    #[Assert\Positive]
    private ?string $density = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    #[Assert\Type(
        type: 'integer',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    private ?int $hardness = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    private ?string $fracture = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Type(
        type: 'string',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    private ?string $streak = null;

    #[ORM\ManyToOne(inversedBy: 'minerals')]
    #[ORM\JoinColumn(nullable: true, onDelete:"SET NULL")]
    private ?Category $category = null;

    #[ORM\ManyToMany(targetEntity: Color::class, mappedBy: 'minerals')]
    private Collection $colors;

    #[ORM\ManyToMany(targetEntity: Lustre::class, mappedBy: 'minerals')]
    private Collection $lustres;

    #[ORM\OneToMany(mappedBy: 'mineral', targetEntity: Variety::class)]
    private Collection $varieties;

    #[ORM\OneToMany(mappedBy: 'mineral', cascade: ['persist'], orphanRemoval: true, targetEntity: Image::class)]
    private Collection $images;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'mineral', targetEntity: Discussion::class, orphanRemoval: true)]
    private Collection $discussions;

    #[ORM\ManyToMany(targetEntity: Coordinate::class, cascade: ['persist'], inversedBy: 'minerals')]
    private Collection $coordinates;

    #[ORM\OneToMany(mappedBy: 'mineral', targetEntity: ModificationHistory::class)]
    private Collection $modificationHistories;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->lustres = new ArrayCollection();
        $this->varieties = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
        $this->discussions = new ArrayCollection();
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

    public function getDensity(): ?string
    {
        return $this->density;
    }

    public function setDensity(?string $density): static
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
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setMineral($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getMineral() === $this) {
                $discussion->setMineral(null);
            }
        }

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
            $coordinate->addMineral($this);
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
            $modificationHistory->setMineral($this);
        }

        return $this;
    }

    public function removeModificationHistory(ModificationHistory $modificationHistory): static
    {
        if ($this->modificationHistories->removeElement($modificationHistory)) {
            // set the owning side to null (unless already changed)
            if ($modificationHistory->getMineral() === $this) {
                $modificationHistory->setMineral(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

}
