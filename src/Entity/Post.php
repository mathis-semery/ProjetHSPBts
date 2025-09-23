<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $texte = null;

    #[ORM\Column]
    private ?\DateTime $dateHeure = null;

    #[ORM\OneToOne(mappedBy: 'refPoste', cascade: ['persist', 'remove'])]
    private ?Reponse $refReponse = null;

    /**
     * @var Collection<int, Reponse>
     */
    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'refPost', orphanRemoval: true)]
    private Collection $reponses;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Canal $refCanal = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $refUser = null;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }

    public function getDateHeure(): ?\DateTime
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTime $dateHeure): static
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    public function getRefReponse(): ?Reponse
    {
        return $this->refReponse;
    }

    public function setRefReponse(Reponse $refReponse): static
    {
        // set the owning side of the relation if necessary
        if ($refReponse->getRefPoste() !== $this) {
            $refReponse->setRefPoste($this);
        }

        $this->refReponse = $refReponse;

        return $this;
    }
    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setRefPost($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getRefPost() === $this) {
                $reponse->setRefPost(null);
            }
        }

        return $this;
    }

    public function getRefCanal(): ?Canal
    {
        return $this->refCanal;
    }

    public function setRefCanal(?Canal $refCanal): static
    {
        $this->refCanal = $refCanal;

        return $this;
    }

    public function getRefUser(): ?user
    {
        return $this->refUser;
    }

    public function setRefUser(?user $refUser): static
    {
        $this->refUser = $refUser;

        return $this;
    }
}
