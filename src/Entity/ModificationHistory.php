<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Mineral;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ModificationHistoryRepository;

#[ORM\Entity(repositoryClass: ModificationHistoryRepository::class)]
class ModificationHistory
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null; // L'utilisateur qui a effectué la modification

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true, onDelete:"SET NULL")]
    private ?Mineral $mineral = null; // L'entité Mineral qui a été modifiée

    #[ORM\Column(type:"json")]
    private $changes; // Les modifications apportées, stockées au format JSON

    #[ORM\Column(type:"datetime")]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Color $color = null;

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Lustre $lustre = null;

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Image $image = null;

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Coordinate $coordinate = null;

    #[ORM\ManyToOne(inversedBy: 'modificationHistories')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Variety $variety = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChanges(): ?array
    {
        return json_decode($this->changes, true);
    }

    public function setChanges(array $changes): static
    {
        $this->changes = json_encode($changes);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getLustre(): ?Lustre
    {
        return $this->lustre;
    }

    public function setLustre(?Lustre $lustre): static
    {
        $this->lustre = $lustre;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCoordinate(): ?Coordinate
    {
        return $this->coordinate;
    }

    public function setCoordinate(?Coordinate $coordinate): static
    {
        $this->coordinate = $coordinate;

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
}
