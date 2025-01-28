<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $editor = null;

    #[ORM\ManyToOne(inversedBy: 'exercices')]
    private ?Cour $cour = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corrected = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_link = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_img = null;


    #[ORM\Column]
    private ?int $sort = null;

    #[ORM\Column]
    private ?bool $is_container = null;

    public function __construct()
    {
        $this->slug = $this->generateSlug();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function generateSlug(): string
    {
        $slugger = new AsciiSlugger('fr');
        $baseSlug = $slugger->slug(strtolower($this->title));

        // Ajout d'un court hash pour l'unicité
        $shortHash = substr(md5(uniqid()), 0, 5);
        return sprintf('%s-%s', $baseSlug, $shortHash);
    }
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getEditor(): ?User
    {
        return $this->editor;
    }

    public function setEditor(?User $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getCour(): ?Cour
    {
        return $this->cour;
    }

    public function setCour(?Cour $cour): static
    {
        $this->cour = $cour;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        $this->slug = $this->generateSlug();
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

    public function getCorrected(): ?string
    {
        return $this->corrected;
    }

    public function setCorrected(?string $corrected): static
    {
        $this->corrected = $corrected;

        return $this;
    }

    public function getVideoLink(): ?string
    {
        return $this->video_link;
    }

    public function setVideoLink(?string $video_link): static
    {
        $this->video_link = $video_link;

        return $this;
    }

    public function getVideoImg(): ?string
    {
        return $this->video_img;
    }

    public function setVideoImg(?string $video_img): static
    {
        $this->video_img = $video_img;

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

    public function isContainer(): ?bool
    {
        return $this->is_container;
    }

    public function setContainer(bool $is_container): static
    {
        $this->is_container = $is_container;

        return $this;
    }
}
