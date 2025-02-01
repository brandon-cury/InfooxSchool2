<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Vich\Uploadable]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("comment")]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;



    #[ORM\Column(length: 255)]
    #[Groups("comment")]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $email_verified_at = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $code_tel = null;

    #[ORM\ManyToOne]
    private ?User $user_affiliate = null;

    #[ORM\Column]
    private ?int $number_affiliated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("comment")]
    private ?string $avatar = null;


    #[Vich\UploadableField(mapping: 'avatar', fileNameProperty: 'avatar')]
    private ?File $avatarFile = null;

    /**
     * @var Collection<int, Bord>
     */
    #[ORM\OneToMany(targetEntity: Bord::class, mappedBy: 'editor')]
    private Collection $myBooksPublished;

    /**
     * @var Collection<int, CollectionBord>
     */
    #[ORM\OneToMany(targetEntity: CollectionBord::class, mappedBy: 'editor')]
    private Collection $collectionBords;

    /**
     * @var Collection<int, Epreuve>
     */
    #[ORM\OneToMany(targetEntity: Epreuve::class, mappedBy: 'editor')]
    private Collection $myEpreuvesPublished;

    /**
     * @var Collection<int, UserBord>
     */
    #[ORM\OneToMany(targetEntity: UserBord::class, mappedBy: 'user')]
    private Collection $userBords;

    /**
     * @var Collection<int, MoneyWithdrawal>
     */
    #[ORM\OneToMany(targetEntity: MoneyWithdrawal::class, mappedBy: 'user')]
    private Collection $moneyWithdrawals;

    #[ORM\Column]
    private bool $isVisible = true;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    public function __construct()
    {
        $this->myBooksPublished = new ArrayCollection();
        $this->collectionBords = new ArrayCollection();
        $this->myEpreuvesPublished = new ArrayCollection();
        $this->userBords = new ArrayCollection();
        $this->moneyWithdrawals = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt(?\DateTimeImmutable $email_verified_at): static
    {
        $this->email_verified_at = $email_verified_at;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getCodeTel(): ?string
    {
        return $this->code_tel;
    }

    public function setCodeTel(?string $code_tel): static
    {
        $this->code_tel = $code_tel;

        return $this;
    }

    public function getUserAffiliate(): ?user
    {
        return $this->user_affiliate;
    }

    public function setUserAffiliate(?user $user_affiliate): static
    {
        $this->user_affiliate = $user_affiliate;

        return $this;
    }

    public function getNumberAffiliated(): ?int
    {
        return $this->number_affiliated;
    }

    public function setNumberAffiliated(int $number_affiliated): static
    {
        $this->number_affiliated = $number_affiliated;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }


    /**
     * @return Collection<int, Bord>
     */
    public function getMyBooksPublished(): Collection
    {
        return $this->myBooksPublished;
    }

    public function addMyBooksPublished(Bord $myBooksPublished): static
    {
        if (!$this->myBooksPublished->contains($myBooksPublished)) {
            $this->myBooksPublished->add($myBooksPublished);
            $myBooksPublished->setEditor($this);
        }

        return $this;
    }

    public function removeMyBooksPublished(Bord $myBooksPublished): static
    {
        if ($this->myBooksPublished->removeElement($myBooksPublished)) {
            // set the owning side to null (unless already changed)
            if ($myBooksPublished->getEditor() === $this) {
                $myBooksPublished->setEditor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollectionBord>
     */
    public function getCollectionBords(): Collection
    {
        return $this->collectionBords;
    }

    public function addCollectionBord(CollectionBord $collectionBord): static
    {
        if (!$this->collectionBords->contains($collectionBord)) {
            $this->collectionBords->add($collectionBord);
            $collectionBord->setEditor($this);
        }

        return $this;
    }

    public function removeCollectionBord(CollectionBord $collectionBord): static
    {
        if ($this->collectionBords->removeElement($collectionBord)) {
            // set the owning side to null (unless already changed)
            if ($collectionBord->getEditor() === $this) {
                $collectionBord->setEditor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Epreuve>
     */
    public function getMyEpreuvesPublished(): Collection
    {
        return $this->myEpreuvesPublished;
    }

    public function addMyEpreuvesPublished(Epreuve $myEpreuvesPublished): static
    {
        if (!$this->myEpreuvesPublished->contains($myEpreuvesPublished)) {
            $this->myEpreuvesPublished->add($myEpreuvesPublished);
            $myEpreuvesPublished->setEditor($this);
        }

        return $this;
    }

    public function removeMyEpreuvesPublished(Epreuve $myEpreuvesPublished): static
    {
        if ($this->myEpreuvesPublished->removeElement($myEpreuvesPublished)) {
            // set the owning side to null (unless already changed)
            if ($myEpreuvesPublished->getEditor() === $this) {
                $myEpreuvesPublished->setEditor(null);
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
            $userBord->setUser($this);
        }

        return $this;
    }

    public function removeUserBord(UserBord $userBord): static
    {
        if ($this->userBords->removeElement($userBord)) {
            // set the owning side to null (unless already changed)
            if ($userBord->getUser() === $this) {
                $userBord->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MoneyWithdrawal>
     */
    public function getMoneyWithdrawals(): Collection
    {
        return $this->moneyWithdrawals;
    }

    public function addMoneyWithdrawal(MoneyWithdrawal $moneyWithdrawal): static
    {
        if (!$this->moneyWithdrawals->contains($moneyWithdrawal)) {
            $this->moneyWithdrawals->add($moneyWithdrawal);
            $moneyWithdrawal->setUser($this);
        }

        return $this;
    }

    public function removeMoneyWithdrawal(MoneyWithdrawal $moneyWithdrawal): static
    {
        if ($this->moneyWithdrawals->removeElement($moneyWithdrawal)) {
            // set the owning side to null (unless already changed)
            if ($moneyWithdrawal->getUser() === $this) {
                $moneyWithdrawal->setUser(null);
            }
        }

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }


    //pour permettre lupload de limage sur la table user, on doit ajouter les fonction de serialisation pour evité des problème de serialisation
    public function __serialize(): array
    {
        return [
            $this->id,
            $this->email,
            $this->password,
        ];
    }
    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->email,
            $this->password,
        ] = $data;
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return  $this->last_name . ' ' .  $this->first_name;
    }

}
