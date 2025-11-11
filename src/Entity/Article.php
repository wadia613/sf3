<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\ArticleRepository')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Le nom doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Le nom doit comporter au maximum {{ limit }} caractères'
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire')]
    #[Assert\Positive(message: 'Le prix doit être supérieur à 0')]
    #[Assert\NotEqualTo(
        value: 0,
        message: 'Le prix ne doit pas être égal à 0'
    )]
    private ?string $prix = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom . ' - ' . $this->prix . '€';
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}