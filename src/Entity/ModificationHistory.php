<?php

namespace App\Entity;

use App\Entity\Mineral;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModificationHistoryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ModificationHistoryRepository::class)]
class ModificationHistory
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\User", inversedBy:"modificationHistories")]
    #[ORM\JoinColumn(nullable:false)]
    private ?UserInterface $user = null; // L'utilisateur qui a effectué la modification

    #[ORM\ManyToOne(targetEntity:"App\Entity\Mineral", inversedBy:"modificationHistories")]
    #[ORM\JoinColumn(nullable:false)]
    private ?Mineral $mineral = null; // L'entité Mineral qui a été modifiée

    #[ORM\Column(type:"json")]
    private array $changes = []; // Les modifications apportées, stockées au format JSON

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getmineral(): ?Mineral
    {
        return $this->mineral;
    }

    public function setmineral(Mineral $mineral): self
    {
        $this->mineral = $mineral;

        return $this;
    }

    public function getChanges(): ?array
    {
        return $this->changes;
    }

    public function setChanges(array $changes): self
    {
        $this->changes = $changes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
