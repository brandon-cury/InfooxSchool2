<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("bord")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("bord")]
    private ?string $path = null;
    #[ORM\Column]
    #[Groups("bord")]
    private ?int $sort = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Bord $bord = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }
    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
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
}
