<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $texte = null;

    #[ORM\Column]
    private ?\DateTime $dateHeure = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $refPost = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refUser = null;
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    public function getId(): ?int { return $this->id; }
    public function getTexte(): ?string { return $this->texte; }
    public function setTexte(string $texte): static { $this->texte = $texte; return $this; }

    public function getDateHeure(): ?\DateTime { return $this->dateHeure; }
    public function setDateHeure(\DateTime $dateHeure): static { $this->dateHeure = $dateHeure; return $this; }

    public function getRefPost(): ?Post { return $this->refPost; }
    public function setRefPost(?Post $refPost): static { $this->refPost = $refPost; return $this; }

    public function getRefUser(): ?User { return $this->refUser; }
    public function setRefUser(?User $refUser): static { $this->refUser = $refUser; return $this; }
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?self { return $this->parent; }
    public function setParent(?self $parent): static { $this->parent = $parent; return $this; }
    public function getChildren(): Collection { return $this->children; }
}
