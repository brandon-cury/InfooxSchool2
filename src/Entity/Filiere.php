<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("filiere")] //j'ai ajouter ceci pour pouvoir selectionner id quand je veux optenir uniquement les propriété de la table filiere
    private ?int $id = null;

    /**
     * @var Collection<int, Section>
     */
    #[ORM\ManyToMany(targetEntity: Section::class, inversedBy: 'filieres')]
    private Collection $section;

    #[ORM\Column(length: 255)]
    #[Groups("filiere")]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups("filiere")]
    private ?int $sort = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups("filiere")]
    private ?string $all_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("filiere")]
    private ?string $image = null;

    /**
     * @var Collection<int, Bord>
     */
    #[ORM\ManyToMany(targetEntity: Bord::class, mappedBy: 'filiere')]
    private Collection $bords;

    /**
     * @var Collection<int, Epreuve>
     */
    #[ORM\ManyToMany(targetEntity: Epreuve::class, mappedBy: 'filiere')]
    private Collection $epreuves;

    #[ORM\Column]
    #[Groups("filiere")]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(targetEntity: Classe::class, mappedBy: 'filiere')]
    private Collection $classes;

    public function __construct()
    {
        $this->section = new ArrayCollection();
        $this->bords = new ArrayCollection();
        $this->epreuves = new ArrayCollection();
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getAllUser(): ?string
    {
        return $this->all_user;
    }

    public function setAllUser(string $all_user): static
    {
        $this->all_user = $all_user;

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

    /**
     * @return Collection<int, Bord>
     */
    public function getBords(): Collection
    {
        return $this->bords;
    }

    public function addBord(Bord $bord): static
    {
        if (!$this->bords->contains($bord)) {
            $this->bords->add($bord);
            $bord->addFiliere($this);
        }

        return $this;
    }

    public function removeBord(Bord $bord): static
    {
        if ($this->bords->removeElement($bord)) {
            $bord->removeFiliere($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Epreuve>
     */
    public function getEpreuves(): Collection
    {
        return $this->epreuves;
    }

    public function addEpreuve(Epreuve $epreuve): static
    {
        if (!$this->epreuves->contains($epreuve)) {
            $this->epreuves->add($epreuve);
            $epreuve->addFiliere($this);
        }

        return $this;
    }

    public function removeEpreuve(Epreuve $epreuve): static
    {
        if ($this->epreuves->removeElement($epreuve)) {
            $epreuve->removeFiliere($this);
        }

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

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setFiliere($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getFiliere() === $this) {
                $class->setFiliere(null);
            }
        }

        return $this;
    }
}
