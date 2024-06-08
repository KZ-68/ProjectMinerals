<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: true, onDelete:"CASCADE")]
    private ?Comment $comment = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: true, onDelete:"CASCADE")]
    private ?Discussion $discussion = null;

    #[ORM\Column]
    private ?bool $upvote = null;

    #[ORM\Column]
    private ?bool $downvote = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function prePersist() {
        if($this->upvote === true) {
            $this->downvote = false;
        } elseif ($this->downvote === true) {
            $this->upvote = false;
        }
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

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): static
    {
        $this->discussion = $discussion;

        return $this;
    }

}
