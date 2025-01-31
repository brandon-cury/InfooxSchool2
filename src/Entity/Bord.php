<?php

namespace App\Entity;

use App\Repository\BordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BordRepository::class)]
class Bord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("bord")]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups("bord")]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'myBooksPublished')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $editor = null;

    #[ORM\Column(length: 255)]
    #[Groups("bord")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups("bord")]
    private ?string $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("bord")]
    private ?string $keyword = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups("bord")]
    private ?string $all_user = null;

    #[ORM\Column]
    #[Groups("bord")]
    private ?int $star = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups("bord")]
    private ?string $price = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups("bord")]
    private ?string $path = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups("bord")]
    private ?string $all_gain_bord = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups("bord")]
    private ?string $all_gain_infooxschool = null;

    #[ORM\Column(nullable: true)]
    #[Groups("bord")]
    private ?\DateTimeImmutable $last_update_at = null;

    #[ORM\Column]
    #[Groups("bord")]
    private ?bool $is_published = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("bord")]
    private ?string $small_description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("bord")]
    private ?string $full_description = null;

    #[ORM\ManyToOne(inversedBy: 'bords')]
    private ?CollectionBord $collection = null;

    /**
     * @var Collection<int, Section>
     */
    #[ORM\ManyToMany(targetEntity: Section::class, inversedBy: 'bords')]
    private Collection $section;

    /**
     * @var Collection<int, Filiere>
     */
    #[ORM\ManyToMany(targetEntity: Filiere::class, inversedBy: 'bords')]
    private Collection $filiere;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'bords')]
    private Collection $classe;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'bords')]
    private Collection $matiere;

    /**
     * @var Collection<int, Cour>
     */
    #[ORM\OneToMany(targetEntity: Cour::class, mappedBy: 'bord')]
    private Collection $cours;

    /**
     * @var Collection<int, Epreuve>
     */
    #[ORM\OneToMany(targetEntity: Epreuve::class, mappedBy: 'bord')]
    private Collection $epreuves;

    /**
     * @var Collection<int, UserBord>
     */
    #[ORM\OneToMany(targetEntity: UserBord::class, mappedBy: 'bord')]
    private Collection $userBords;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'bord')]
    #[Groups("bord")]
    private Collection $images;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'bord')]
    private Collection $comments;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups("bord")]
    private ?string $numb_page = null;

    #[ORM\Column]
    #[Groups("bord")]
    private ?\DateTimeImmutable $created_at = null;


    public function __construct()
    {
        $this->section = new ArrayCollection();
        $this->filiere = new ArrayCollection();
        $this->classe = new ArrayCollection();
        $this->matiere = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->epreuves = new ArrayCollection();
        $this->userBords = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->slug = $this->generateSlug();
        $this->path = $this->generatePath();
        $this->comments = new ArrayCollection();
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
    public function generatePath(): string
    {
        //$date = new \DateTime();
        //$formattedDate = $date->format('YmdHis');

        return 'bord_' . uniqid();
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): static
    {
        $this->keyword = $keyword;

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

    public function getStar(): ?int
    {
        return $this->star;
    }

    public function setStar(int $star): static
    {
        $this->star = $star;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
    public function getAllGainBord(): ?string
    {
        return $this->all_gain_bord;
    }

    public function setAllGainBord(string $all_gain_bord): static
    {
        $this->all_gain_bord = $all_gain_bord;

        return $this;
    }

    public function getAllGainInfooxschool(): ?string
    {
        return $this->all_gain_infooxschool;
    }

    public function setAllGainInfooxschool(string $all_gain_infooxschool): static
    {
        $this->all_gain_infooxschool = $all_gain_infooxschool;

        return $this;
    }

    public function getLastUpdateAt(): ?\DateTimeImmutable
    {
        return $this->last_update_at;
    }

    public function setLastUpdateAt(?\DateTimeImmutable $last_update_at): static
    {
        $this->last_update_at = $last_update_at;

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
    public function getSmallDescription(): ?string
    {
        return $this->small_description;
    }

    public function setSmallDescription(?string $small_description): static
    {
        $this->small_description = $small_description;

        return $this;
    }

    public function getFullDescription(): ?string
    {
        return $this->full_description;
    }

    public function setFullDescription(?string $full_description): static
    {
        $this->full_description = $full_description;

        return $this;
    }

    public function getCollection(): ?CollectionBord
    {
        return $this->collection;
    }

    public function setCollection(?CollectionBord $collection): static
    {
        $this->collection = $collection;

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

    /**
     * @return Collection<int, Cour>
     */
    public function getCours(): Collection
    {
        $iterator = $this->cours->getIterator();

        // Trier les cours par le champ assort
        $iterator->uasort(function ($first, $second) {
            return $first->getSort() <=> $second->getSort();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addCour(Cour $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setBord($this);
        }
        return $this;
    }

    public function removeCour(Cour $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getBord() === $this) {
                $cour->setBord(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Epreuve>
     */
    public function getEpreuves(): Collection
    {
        $iterator = $this->epreuves->getIterator();
        // Trier les cours par le champ assort
        $iterator->uasort(function ($first, $second) {
            return $first->getSort() <=> $second->getSort();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addEpreuve(Epreuve $epreufe): static
    {
        if (!$this->epreuves->contains($epreufe)) {
            $this->epreuves->add($epreufe);
            $epreufe->setBord($this);
        }

        return $this;
    }

    public function removeEpreuve(Epreuve $epreufe): static
    {
        if ($this->epreuves->removeElement($epreufe)) {
            // set the owning side to null (unless already changed)
            if ($epreufe->getBord() === $this) {
                $epreufe->setBord(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserBord>
     */
    public function getUserBords(): Collection
    {
        return $this->userBords;
    }

    public function addUserBord(UserBord $userBord): static
    {
        if (!$this->userBords->contains($userBord)) {
            $this->userBords->add($userBord);
            $userBord->setBord($this);
        }

        return $this;
    }

    public function removeUserBord(UserBord $userBord): static
    {
        if ($this->userBords->removeElement($userBord)) {
            // set the owning side to null (unless already changed)
            if ($userBord->getBord() === $this) {
                $userBord->setBord(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setBord($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBord() === $this) {
                $image->setBord(null);
            }
        }

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
            $comment->setBord($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBord() === $this) {
                $comment->setBord(null);
            }
        }

        return $this;
    }

    public function getNumbPage(): ?string
    {
        return $this->numb_page;
    }

    public function setNumbPage(?string $numb_page): static
    {
        $this->numb_page = $numb_page;

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
}
