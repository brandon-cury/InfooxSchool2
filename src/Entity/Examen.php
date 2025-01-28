<?php

namespace App\Entity;

use App\Repository\ExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExamenRepository::class)]
class Examen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Examen")] //j'ai ajouter ceci pour pouvoir selectionner id quand je veux optenir uniquement les propriété de la table examen
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("Examen")]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("Examen")]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups("Examen")]
    private ?int $sort = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(targetEntity: Classe::class, mappedBy: 'examen')]
    private Collection $classes;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
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
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        $iterator = $this->classes->getIterator();
        $iterator->uasort(function ($first, $second) {
            return $second->getAllUser() <=> $first->getAllUser();
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setExamen($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getExamen() === $this) {
                $class->setExamen(null);
            }
        }

        return $this;
    }
}
