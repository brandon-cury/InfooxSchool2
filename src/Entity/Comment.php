<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("comment")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[Groups("comment")]
    private ?Bord $bord = null;


    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("comment")]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups("comment")]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("comment")]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups("comment")]
    private ?bool $is_published = null;

    #[ORM\Column]
    #[Groups("comment")]
    private ?int $rating = null;

    #[ORM\Column]
    private ?bool $is_send = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBord(): ?Bord
    {
        return $this->bord;
    }

    public function setBord(?Bord $bord): static
    {
        $this->bord = $bord;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setPublished(bool $is_published): static
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function isSend(): ?bool
    {
        return $this->is_send;
    }

    public function setSend(bool $is_send): static
    {
        $this->is_send = $is_send;

        return $this;
    }
}
