<?php

namespace App\Entity;

use App\Repository\EpreuveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: EpreuveRepository::class)]
class Epreuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'myEpreuvesPublished')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $editor = null;

    #[ORM\ManyToOne(inversedBy: 'epreuves')]
    private ?Bord $bord = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $all_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $corrected = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_link = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_img = null;

    #[ORM\Column]
    private ?bool $is_youtube = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $sort = null;

    /**
     * @var Collection<int, Section>
     */
    #[ORM\ManyToMany(targetEntity: Section::class, inversedBy: 'epreuves')]
    private Collection $section;

    /**
     * @var Collection<int, Filiere>
     */
    #[ORM\ManyToMany(targetEntity: Filiere::class, inversedBy: 'epreuves')]
    private Collection $filiere;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'epreuves')]
    private Collection $classe;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'epreuves')]
    private Collection $matiere;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    #[ORM\Column]
    private ?int $star = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\Column]
    private ?bool $is_container = null;

    #[ORM\Column]
    private ?bool $is_onligne = null;

    public function __construct()
    {
        $this->section = new ArrayCollection();
        $this->filiere = new ArrayCollection();
        $this->classe = new ArrayCollection();
        $this->matiere = new ArrayCollection();
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

    public function getEditor(): ?user
    {
        return $this->editor;
    }

    public function setEditor(?user $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getBord(): ?bord
    {
        return $this->bord;
    }

    public function setBord(?bord $bord): static
    {
        $this->bord = $bord;

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

    public function getAllUser(): ?string
    {
        return $this->all_user;
    }

    public function setAllUser(string $all_user): static
    {
        $this->all_user = $all_user;

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

    public function isYoutube(): ?bool
    {
        return $this->is_youtube;
    }

    public function setYoutube(bool $is_youtube): static
    {
        $this->is_youtube = $is_youtube;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    /**
     * @return Collection<int, Section>
     */
    public function getSection(): Collection
    {
        return $this->section;
    }

    public function addSection(Section $section): static
    {
        if (!$this->section->contains($section)) {
            $this->section->add($section);
        }

        return $this;
    }

    public function removeSection(Section $section): static
    {
        $this->section->removeElement($section);

        return $this;
    }

    /**
     * @return Collection<int, Filiere>
     */
    public function getFiliere(): Collection
    {
        return $this->filiere;
    }

    public function addFiliere(Filiere $filiere): static
    {
        if (!$this->filiere->contains($filiere)) {
            $this->filiere->add($filiere);
        }

        return $this;
    }

    public function removeFiliere(Filiere $filiere): static
    {
        $this->filiere->removeElement($filiere);

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasse(): Collection
    {
        return $this->classe;
    }

    public function addClasse(Classe $classe): static
    {
        if (!$this->classe->contains($classe)) {
            $this->classe->add($classe);
        }

        return $this;
    }

    public function removeClasse(Classe $classe): static
    {
        $this->classe->removeElement($classe);

        return $this;
    }

    /**
     * @return Collection<int, Matiere>
     */
    public function getMatiere(): Collection
    {
        return $this->matiere;
    }

    public function addMatiere(Matiere $matiere): static
    {
        if (!$this->matiere->contains($matiere)) {
            $this->matiere->add($matiere);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): static
    {
        $this->matiere->removeElement($matiere);

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getStar(): ?int
    {
        return $this->star;
    }

    public function setStar(int $star): static
    {
        $this->star = $star;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTimeImmutable $update_at): static
    {
        $this->update_at = $update_at;

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

    public function isOnligne(): ?bool
    {
        return $this->is_onligne;
    }

    public function setOnligne(bool $is_onligne): static
    {
        $this->is_onligne = $is_onligne;

        return $this;
    }
}
