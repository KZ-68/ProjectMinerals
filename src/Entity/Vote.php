<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comment $comment = null;

    #[ORM\Column]
    private ?bool $upvote = null;

    #[ORM\Column]
    private ?bool $downvote = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getDateTime()
    {
        return $this->createdAt->format("d/m/Y H:i:s")."";
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function isUpvote(): ?bool
    {
        return $this->upvote;
    }

    public function setUpvote(bool $upvote): static
    {
        $this->upvote = $upvote;

        return $this;
    }

    public function isDownvote(): ?bool
    {
        return $this->downvote;
    }

    public function setDownvote(bool $downvote): static
    {
        $this->downvote = $downvote;

        return $this;
    }

}
