<?php

namespace App\Entity;

use App\Repository\CollectionBordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionBordRepository::class)]
class CollectionBord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'collectionBords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $editor = null;

    /**
     * @var Collection<int, Bord>
     */
    #[ORM\OneToMany(targetEntity: Bord::class, mappedBy: 'collection')]
    private Collection $bords;

    public function __construct()
    {
        $this->bords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEditor(): ?User
    {
        return $this->editor;
    }

    public function setEditor(?User $editor): static
    {
        $this->editor = $editor;

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
            $bord->setCollection($this);
        }

        return $this;
    }

    public function removeBord(Bord $bord): static
    {
        if ($this->bords->removeElement($bord)) {
            // set the owning side to null (unless already changed)
            if ($bord->getCollection() === $this) {
                $bord->setCollection(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->title;
    }
}
