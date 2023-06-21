<?php

namespace App\Entity;

use App\Repository\GrilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GrilleRepository::class)]
class Grille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0)]
    private ?int $hauteur = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0)]
    private ?int $largeur = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHauteur(): ?int
    {
        return $this->hauteur;
    }

    public function setHauteur(int $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->largeur;
    }

    public function setLargeur(int $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }
}
