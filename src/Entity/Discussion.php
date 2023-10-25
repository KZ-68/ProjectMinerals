<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource()]
#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
class Discussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 2)]
    #[Assert\NotBlank()]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mineral $mineral = null;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column]
    private ?bool $isDeletedByModerator = false;

    #[ORM\Column]
    private ?bool $isDeletedByUser = false;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setDiscussion($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getDiscussion() === $this) {
                $comment->setDiscussion(null);
            }
        }

        return $this;
    }

    public function isIsDeletedByModerator(): ?bool
    {
        return $this->isDeletedByModerator;
    }

    public function setIsDeletedByModerator(bool $isDeletedByModerator): static
    {
        $this->isDeletedByModerator = $isDeletedByModerator;

        return $this;
    }

    public function getDateTime()
    {
        return $this->createdAt->format("d/m/Y H:i:s")."";
    }

    public function isIsDeletedByUser(): ?bool
    {
        return $this->isDeletedByUser;
    }

    public function setIsDeletedByUser(bool $isDeletedByUser): static
    {
        $this->isDeletedByUser = $isDeletedByUser;

        return $this;
    }

}
