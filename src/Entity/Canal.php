<?php

namespace App\Entity;

use App\Repository\CanalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CanalRepository::class)]
class Canal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $ListeAuto = null;

    #[ORM\OneToOne(mappedBy: 'refCanal', cascade: ['persist', 'remove'])]
    private ?Post $refPost = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'refCanal', orphanRemoval: true)]
    private Collection $posts;

    #[ORM\ManyToOne(inversedBy: 'canals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $refUser = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getListeAuto(): ?string
    {
        return $this->ListeAuto;
    }

    public function setListeAuto(?string $ListeAuto): static
    {
        $this->ListeAuto = $ListeAuto;

        return $this;
    }

    public function getRefPost(): ?Post
    {
        return $this->refPost;
    }

    public function setRefPost(Post $refPost): static
    {
        // set the owning side of the relation if necessary
        if ($refPost->getRefCanal() !== $this) {
            $refPost->setRefCanal($this);
        }

        $this->refPost = $refPost;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setRefCanal($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getRefCanal() === $this) {
                $post->setRefCanal(null);
            }
        }

        return $this;
    }

    public function getRefUser(): ?User
    {
        return $this->refUser;
    }

    public function setRefUser(?User $refUser): static
    {
        $this->refUser = $refUser;

        return $this;
    }
}
