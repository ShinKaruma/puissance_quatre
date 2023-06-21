<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $scoreP1 = null;

    #[ORM\Column]
    private ?int $scoreP2 = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grille $Grille = null;

    #[ORM\ManyToOne(inversedBy: 'Partie')]
    private ?User $player1 = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    private ?User $player2 = null;

    #[ORM\OneToMany(mappedBy: 'Partie', targetEntity: Pion::class)]
    private Collection $pions;

    public function __construct()
    {
        $this->pions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScoreP1(): ?int
    {
        return $this->scoreP1;
    }

    public function setScoreP1(int $scoreP1): self
    {
        $this->scoreP1 = $scoreP1;

        return $this;
    }

    public function getScoreP2(): ?int
    {
        return $this->scoreP2;
    }

    public function setScoreP2(int $scoreP2): self
    {
        $this->scoreP2 = $scoreP2;

        return $this;
    }

    public function getGrille(): ?Grille
    {
        return $this->Grille;
    }

    public function setGrille(Grille $Grille): self
    {
        $this->Grille = $Grille;

        return $this;
    }

    public function getPlayer1(): ?User
    {
        return $this->player1;
    }

    public function setPlayer1(?User $player1): self
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getPlayer2(): ?User
    {
        return $this->player2;
    }

    public function setPlayer2(?User $player2): self
    {
        $this->player2 = $player2;

        return $this;
    }

    /**
     * @return Collection<int, Pion>
     */
    public function getPions(): Collection
    {
        return $this->pions;
    }

    public function addPion(Pion $pion): self
    {
        if (!$this->pions->contains($pion)) {
            $this->pions->add($pion);
            $pion->setPartie($this);
        }

        return $this;
    }

    public function removePion(Pion $pion): self
    {
        if ($this->pions->removeElement($pion)) {
            // set the owning side to null (unless already changed)
            if ($pion->getPartie() === $this) {
                $pion->setPartie(null);
            }
        }

        return $this;
    }

    function checkPosLibre(int $posVer, int $posHor) : ?Pion {
        foreach($this->pions as $pion){
            if ($pion->checkPos($posVer, $posHor)) {
                return $pion;
            }
        }  
        return null;      
    }

    function is_full() : bool {
        for ($i=0; $i < $this->Grille->getHauteur(); $i++) { 
            for ($j=0; $j < $this->Grille->getLargeur(); $j++) { 
                if ($this->checkPosLibre($i, $j) == null) {
                    return false;
                } 
            }
        }
        return true;
    }
    
}
