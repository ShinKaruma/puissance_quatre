<?php

namespace App\Controller;

use App\Entity\Grille;
use App\Entity\Partie;
use App\Entity\Pion;
use App\Entity\User;
use App\Repository\PartieRepository;
use App\Repository\PionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/listgame', name: 'app_listgame')]
    public function listgame(PartieRepository $partieRepository): Response
    {
        return $this->render('game/listgame.html.twig', [
            'parties' => $partieRepository->findBy(['player2' => null]),
        ]);
    }

    #[Route('/partie/{id}/rejoindre', name: 'rejoindre_partie')]
    public function rejoindrePartie(Partie $partie, PartieRepository $partieRepository): Response
    {
        $user2 = $this->getUser();
        $partie->setPlayer2($user2);

        $partieRepository->save($partie, true);

        return $this->redirectToRoute('app_game_play', ['id' => $partie->getId()]);
    }

    #[Route('/game/create', name: 'app_game_create')]
    public function create(PartieRepository $partieRepository): Response
    {
        $user = $this->getUser();


        if (count($user->getParties()) === 0) {
            $partie = new Partie();

            $grille = new Grille();
            $grille->setHauteur(6);
            $grille->setLargeur(7);
            $partie->setGrille($grille);

            $user1 = $this->getUser();
            $partie->setPlayer1($user1);

            $partieRepository->save($partie, true);

            return $this->redirectToRoute('app_game_play', ['id' => $partie->getId()]);
        } else {
            return $this->redirectToRoute('app_listgame');
        }
    }


    #[Route('/game/{id}', name: 'app_game_play')]
    function gamePlay(Partie $partie): Response
    {

        $user = $this->getUser();
        return $this->render('game/index.html.twig', [
            'partie' => $partie,
            'user' => $user
        ]);
    }

    #[Route('/game/test', name: 'app_game_test')]
    public function index(): Response
    {
        // Créer une nouvelle partie avec une grille de 6x7
        $grille = new Grille();
        $grille->setHauteur(6);
        $grille->setLargeur(7);

        $partie = new Partie();
        $partie->setGrille($grille);

        // Récupérer les utilisateurs joueurs de la partie

        $user1 = $this->getUser(); // Utilisateur assigné à la partie (Joueur 1)
        $user2 = $this->getUser(); // Utilisateur assigné à la partie (Joueur 2)

        // Vérifier si les deux joueurs sont présents
        // if (!$user1 || !$user2) {
        //     throw new \Exception("Joueurs non disponibles");
        // }

        // Assigner les joueurs à la partie
        $partie->setPlayer1($user1);
        $partie->setPlayer2($user2);

        return $this->render('game/index.html.twig', [
            'partie' => $partie,
            'grille' => $grille,
        ]);
    }

    #[Route('/game/{id}/turn/{posHor}/{posVer}', name: 'app_game_turn')]
    function playTurn(Partie $partie, int $posHor, int $posVer, PionRepository $pionRepository, PartieRepository $partieRepository): Response
    {
        $user = $this->getUser();

        if ($user == $partie->getPlayer1()) {
            $partie->placerPion($pionRepository, $posHor, $posVer, "#ff0000");
            $partie->setPlayerEnCours($partie->getPlayer2());
        } else if ($user == $partie->getPlayer2()) {
            $partie->placerPion($pionRepository, $posHor, $posVer, "#ffff00");
            $partie->setPlayerEnCours($partie->getPlayer1());
        }

        $partieRepository->save($partie, true);

        return $this->redirectToRoute('app_game_play', ['id' => $partie->getId()]);
    }


    #[Route('/partie/{id}/reprendre', name: 'rejoindre_partie')]
    public function reprendrePartie(Partie $partie): Response
    {
        return $this->redirectToRoute('app_game_play', ['id' => $partie->getId()]);
    }

    #[Route('/listgamereprendre', name: 'app_listgame_reprendre')]
    public function listGameReprendre(PartieRepository $partieRepository): Response
    {
        return $this->render('game/listgame.html.twig', [
            'parties' => $partieRepository->findByPlayer($this->getUser()),
        ]);
    }
}
