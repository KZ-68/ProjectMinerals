<?php

namespace App\Entity;

use DateTimeImmutable;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Polyfill\Intl\Icu\IntlDateFormatter;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exist')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 2)]
    #[Assert\NotBlank()]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?Discussion $discussion = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true, onDelete:"SET NULL")]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'parent')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private Comment|null $parent = null;

    #[ORM\Column]
    private ?bool $isDeletedByModerator = false;

    #[ORM\Column]
    private ?bool $isDeletedByUser = false;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: Vote::class, orphanRemoval: true)]
    private Collection $votes;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->children = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist() {
        $this->slug = (new Slugify())->slugify($this->content.uniqid('-'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): static
    {
        $this->discussion = $discussion;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getDateTime()
    {
        return $this->createdAt->format("d/m/Y H:i:s")."";
    }

    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    public function setChildren(?Comment $children): static
    {
        $this->children = $children;

        return $this;
    }

    public function addChild(Comment $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setChildren($this);
        }

        return $this;
    }

    public function removeChild(Comment $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getChildren() === $this) {
                $child->setChildren(null);
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

    public function isIsDeletedByUser(): ?bool
    {
        return $this->isDeletedByUser;
    }

    public function setIsDeletedByUser(bool $isDeletedByUser): static
    {
        $this->isDeletedByUser = $isDeletedByUser;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setComment($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getComment() === $this) {
                $vote->setComment(null);
            }
        }

        return $this;
    }

}
