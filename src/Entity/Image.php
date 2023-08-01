<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Mineral $mineral = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Variety $variety = null;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
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
}
