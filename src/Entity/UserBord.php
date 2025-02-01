<?php

namespace App\Entity;

use App\Repository\UserBordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBordRepository::class)]
#[ORM\Table(name: 'user_bord')]
#[ORM\UniqueConstraint(
    name: 'unique_user_bord',
    columns: ['user_id', 'bord_id']
)]
class UserBord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userBords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?bord $bord = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $recorded_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end_at = null;

    #[ORM\ManyToOne(inversedBy: 'userBords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user = null;

    #[ORM\Column]
    private ?bool $is_visible = null;

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

    public function getRecordedAt(): ?\DateTimeImmutable
    {
        return $this->recorded_at;
    }

    public function setRecordedAt(\DateTimeImmutable $recorded_at): static
    {
        $this->recorded_at = $recorded_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeImmutable $end_at): static
    {
        $this->end_at = $end_at;

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

    public function isVisible(): ?bool
    {
        return $this->is_visible;
    }

    public function setVisible(bool $is_visible): static
    {
        $this->is_visible = $is_visible;

        return $this;
    }
}
