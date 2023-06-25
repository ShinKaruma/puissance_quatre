<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use App\Entity\Pion;
use App\Repository\PionRepository;
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
    private ?int $scoreP1 = 0;

    #[ORM\Column]
    private ?int $scoreP2 = 0;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grille $Grille = null;

    #[ORM\ManyToOne(inversedBy: 'Partie')]
    private ?User $player1 = null;

    #[ORM\ManyToOne(inversedBy: 'parties')]
    private ?User $player2 = null;

    #[ORM\OneToMany(mappedBy: 'Partie', targetEntity: Pion::class)]
    private Collection $pions;

    #[ORM\ManyToOne]
    private ?User $playerEnCours = null;

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

    function checkPosLibre(int $posVer, int $posHor): ?Pion
    {
        foreach ($this->pions as $pion) {
            if ($pion->checkPos($posVer, $posHor)) {
                return $pion;
            }
        }
        return null;
    }

    function checkIsFull(): bool
    {
        for ($i = 0; $i < $this->Grille->getHauteur(); $i++) {
            for ($j = 0; $j < $this->Grille->getLargeur(); $j++) {
                if ($this->checkPosLibre($i, $j) == null) {
                    return false;
                }
            }
        }
        return true;
    }

    function placerPion(PionRepository $pionRepository, int $posHor, int $posVer, string $couleur): self
    {
        $pion = new Pion();
        $pion->setPosHor($posHor);
        $pion->setPosVer($posVer);
        $pion->setCouleur($couleur);
        $this->addPion($pion);
        $pionRepository->save($pion, true);

        return $this;
    }

    public function getPlayerEnCours(): ?User
    {
        return $this->playerEnCours;
    }

    public function setPlayerEnCours(?User $playerEnCours): static
    {
        $this->playerEnCours = $playerEnCours;

        return $this;
    }

    // Vérifie si une ligne de pions de même couleur existe dans la grille
    public function checkLignes(string $couleur): int
    {
        $hauteur = $this->Grille->getHauteur();
        $largeur = $this->Grille->getLargeur();
        $compteurLignes = 0;

        for ($i = 0; $i < $hauteur; $i++) {
            $pionsLigne = 0;

            for ($j = 0; $j < $largeur; $j++) {
                $pion = $this->checkPosLibre($i, $j);

                if ($pion !== null && $pion->getCouleur() === $couleur) {
                    $pionsLigne++;
                } else {
                    $pionsLigne = 0;
                }

                if ($pionsLigne === 4) {
                    $compteurLignes++;
                    $pionsLigne = 0;
                }
            }
        }

        return $compteurLignes;
    }

    // Vérifie si une colonne de pions de même couleur existe dans la grille
    public function checkColonnes(string $couleur): int
    {
        $hauteur = $this->Grille->getHauteur();
        $largeur = $this->Grille->getLargeur();
        $compteurColonnes = 0;

        for ($j = 0; $j < $largeur; $j++) {
            $pionsColonne = 0;

            for ($i = 0; $i < $hauteur; $i++) {
                $pion = $this->checkPosLibre($i, $j);

                if ($pion !== null && $pion->getCouleur() === $couleur) {
                    $pionsColonne++;
                } else {
                    $pionsColonne = 0;
                }

                if ($pionsColonne === 4) {
                    $compteurColonnes++;
                    $pionsColonne = 0;
                }
            }
        }

        return $compteurColonnes;
    }


    // Vérifie si une diagonale ascendante de pions de même couleur existe dans la grille
    public function checkDiagonalesAsc(string $couleur): int
    {
        $hauteur = $this->Grille->getHauteur();
        $largeur = $this->Grille->getLargeur();
        $compteurDiagonales = 0;

        // Vérification des diagonales ascendantes
        for ($i = 3; $i < $hauteur; $i++) {
            for ($j = 0; $j < $largeur - 3; $j++) {
                $pionsDiagonale = 0;

                for ($k = 0; $k < 4; $k++) {
                    $pion = $this->checkPosLibre($i - $k, $j + $k);

                    if ($pion !== null && $pion->getCouleur() === $couleur) {
                        $pionsDiagonale++;
                    } else {
                        $pionsDiagonale = 0;
                    }

                    if ($pionsDiagonale === 4) {
                        $compteurDiagonales++;
                        $pionsDiagonale = 0;
                    }
                }
            }
        }

        return $compteurDiagonales;
    }

    // Vérifie si une diagonale descendante de pions de même couleur existe dans la grille
    public function checkDiagonalesDesc(string $couleur): int
    {
        $hauteur = $this->Grille->getHauteur();
        $largeur = $this->Grille->getLargeur();
        $compteurDiagonales = 0;

        // Vérification des diagonales descendantes
        for ($i = 0; $i < $hauteur - 3; $i++) {
            for ($j = 0; $j < $largeur - 3; $j++) {
                $pionsDiagonale = 0;

                for ($k = 0; $k < 4; $k++) {
                    $pion = $this->checkPosLibre($i + $k, $j + $k);

                    if ($pion !== null && $pion->getCouleur() === $couleur) {
                        $pionsDiagonale++;
                    } else {
                        $pionsDiagonale = 0;
                    }

                    if ($pionsDiagonale === 4) {
                        $compteurDiagonales++;
                        $pionsDiagonale = 0;
                    }
                }
            }
        }

        return $compteurDiagonales;
    }

    // Calcule le nombre de points pour un joueur en fonction des lignes, colonnes et diagonales gagnantes
    public function calculatePoints(string $couleur): int
    {
        $points = 0;

        if ($this->checkLignes($couleur)) {
            $points += 1;
        }
        if ($this->checkColonnes($couleur)) {
            $points += 1;
        }
        if ($this->checkDiagonalesAsc($couleur)) {
            $points += 1;
        }
        if ($this->checkDiagonalesDesc($couleur)) {
            $points += 1;
        }

        return $points;
    }
}
