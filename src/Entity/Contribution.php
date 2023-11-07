<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ContributionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContributionRepository::class)]
#[ApiResource]
class Contribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mineral $mineral = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $contribution_likes = 0;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContributionLikes(): ?int
    {
        return $this->contribution_likes;
    }

    public function setContributionLikes(int $contribution_likes): static
    {
        $this->contribution_likes = $contribution_likes;

        return $this;
    }
}
