<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255 , unique: true)]
    private ?string $siteWeb = null;

    #[ORM\OneToOne(mappedBy: 'refEntreprise', cascade: ['persist', 'remove'])]
    private ?User $refUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(string $siteWeb): static
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    public function getRefUser(): ?User
    {
        return $this->refUser;
    }

    public function setRefUser(?User $refUser): static
    {
        // unset the owning side of the relation if necessary
        if ($refUser === null && $this->refUser !== null) {
            $this->refUser->setRefEntreprise(null);
        }

        // set the owning side of the relation if necessary
        if ($refUser !== null && $refUser->getRefEntreprise() !== $this) {
            $refUser->setRefEntreprise($this);
        }

        $this->refUser = $refUser;

        return $this;
    }
}
