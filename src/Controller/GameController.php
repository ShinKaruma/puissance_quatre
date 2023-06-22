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

        // Créer deux utilisateurs pour la partie
        $user1 = $this->getUser();
        $user2 = new User(); // Remplacez par la façon dont vous créez un nouvel utilisateur

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

        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
            'partie' => $partie,
            'grille' => $grille,
            'isGrillePleine' => $isGrillePleine,
        ]);
    }
}
