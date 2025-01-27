<?php

namespace App\Entity;

use App\Repository\CourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: CourRepository::class)]
class Cour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bord $bord = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_link = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video_img = null;

    #[ORM\Column]
    private ?bool $is_youtube = null;

    #[ORM\Column]
    private ?int $sort = null;

    #[ORM\Column]
    private ?bool $is_container = null;

    /**
     * @var Collection<int, Exercice>
     */
    #[ORM\OneToMany(targetEntity: Exercice::class, mappedBy: 'cour')]
    private Collection $exercices;


    public function __construct()
    {
        $this->exercices = new ArrayCollection();
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

    public function getBord(): ?Bord
    {
        return $this->bord;
    }

    public function setBord(?Bord $bord): static
    {
        $this->bord = $bord;

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

    /**
     * @return Collection<int, Exercice>
     */
    public function getExercices(): Collection
    {
        $iterator = $this->exercices->getIterator();
        $iterator->uasort(function ($first, $second) {
            return $first->getSort() <=> $second->getSort();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addExercice(Exercice $exercice): static
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices->add($exercice);
            $exercice->setCour($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): static
    {
        if ($this->exercices->removeElement($exercice)) {
            // set the owning side to null (unless already changed)
            if ($exercice->getCour() === $this) {
                $exercice->setCour(null);
            }
        }

        return $this;
    }


}
