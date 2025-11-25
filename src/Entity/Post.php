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

    #[ORM\Column(type: 'text')]
    private ?string $texte = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refUser = null;

    #[ORM\ManyToOne(targetEntity: Canal::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Canal $canal = null;

    #[ORM\OneToMany(mappedBy: 'refPost', targetEntity: Reponse::class, cascade: ['remove'])]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->dateHeure = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getTexte(): ?string { return $this->texte; }
    public function setTexte(string $texte): self { $this->texte = $texte; return $this; }

    public function getRefUser(): ?User { return $this->refUser; }
    public function setRefUser(?User $refUser): self { $this->refUser = $refUser; return $this; }

    public function getCanal(): ?Canal { return $this->canal; }
    public function setCanal(?Canal $canal): self { $this->canal = $canal; return $this; }

    /** @return Collection|Reponse[] */
    public function getReponses(): Collection { return $this->reponses; }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setRefPost($this);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse) && $reponse->getRefPost() === $this) {
            $reponse->setRefPost(null);
        }
        return $this;
    }
}
