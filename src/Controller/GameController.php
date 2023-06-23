<?php

namespace App\Controller;

use App\Entity\Grille;
use App\Entity\Partie;
use App\Entity\Pion;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(): Response
    {
        // Créer une nouvelle partie avec une grille de 6x7
        $grille = new Grille();
        $grille->setHauteur(6);
        $grille->setLargeur(7);

        $partie = new Partie();
        $partie->setGrille($grille);

        // Récupérer les utilisateurs joueurs de la partie
        $user1 = $partie->getPlayer1(); // Utilisateur assigné à la partie (Joueur 1)
        $user2 = $partie->getPlayer2(); // Utilisateur assigné à la partie (Joueur 2)

        // Vérifier si les deux joueurs sont présents
        // if (!$user1 || !$user2) {
        //     throw new \Exception("Joueurs non disponibles");
        // }

        // Assigner les joueurs à la partie
        $partie->setPlayer1($user1);
        $partie->setPlayer2($user2);

        // Créer les pions pour la partie
        for ($i = 0; $i < $grille->getHauteur(); $i++) {
            for ($j = 0; $j < $grille->getLargeur(); $j++) {
                $pion = new Pion();
                $pion->setPosVer($i);
                $pion->setPosHor($j);
                $partie->addPion($pion);
            }
        }

        // Vérifier si la grille est pleine
        $isGrillePleine = $partie->is_full();

        // Placez les pions sur la grille
        foreach ($partie->getPions() as $pion) {
            $grille->placerPion($pion->getPosVer(), $pion->getPosHor(), $pion);
        }

        return $this->render('game/index.html.twig', [
            'partie' => $partie,
            'grille' => $grille,
            'isGrillePleine' => $isGrillePleine,
        ]);
    }
}
