<?php

namespace App\Entity;

use App\Repository\PionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PionRepository::class)]
class Pion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $posVer = null;

    #[ORM\Column]
    private ?int $posHor = null;

    #[ORM\ManyToOne(inversedBy: 'pions')]
    private ?Partie $Partie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosVer(): ?int
    {
        return $this->posVer;
    }

    public function setPosVer(int $posVer): self
    {
        $this->posVer = $posVer;

        return $this;
    }

    public function getPosHor(): ?int
    {
        return $this->posHor;
    }

    public function setPosHor(int $posHor): self
    {
        $this->posHor = $posHor;

        return $this;
    }

    public function getPartie(): ?Partie
    {
        return $this->Partie;
    }

    public function setPartie(?Partie $Partie): self
    {
        $this->Partie = $Partie;

        return $this;
    }

    public function checkPos(int $posVer, int $posHor) : bool {
        if ($posHor == $this->posHor and $posVer == $this->posVer) {
            return true;
        }else{
            return false;
        }
    }
}
