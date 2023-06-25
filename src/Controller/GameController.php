<?php

namespace App\Controller;

use App\Entity\Grille;
use App\Entity\Partie;
use App\Entity\Pion;
use App\Entity\User;
use App\Repository\PartieRepository;
use App\Repository\PionRepository;
use App\Repository\UserRepository;
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

    #[Route('/game/{id}/rejoindre', name: 'rejoindre_partie')]
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

            $partie->setPlayer1($user);
            $partie->setPlayerEnCours($user);

            $partieRepository->save($partie, true);

            return $this->redirectToRoute('app_game_play', ['id' => $partie->getId()]);
        } else {
            return $this->redirectToRoute('app_listgame');
        }
    }


    #[Route('/game/{id}', name: 'app_game_play')]
    function gamePlay(Partie $partie, PartieRepository $partieRepository): Response
    {
        $user = $this->getUser();

        $couleurs = ["#ff0000", "#ffff00"];

        if ($partie->checkIsFull()) {
            $score = 0;
            foreach($couleurs as $couleur){
                $score+= $partie->checkLignes($couleur);
                $score+= $partie->checkColonnes($couleur);
                $score+= $partie->checkDiagonalesAsc($couleur);
                $score+= $partie->checkDiagonalesDesc($couleur);
                if($couleur == "#ff0000"){
                    $partie->setScoreP1($score);
                }else{
                    $partie->setScoreP2($score);
                }
            }
            $partieRepository->save($partie, true);
            return $this->redirectToRoute('app_game_end', ['id' => $partie->getId(), 'scoreP1' => $partie->getScoreP1(), 'scoreP2' => $partie->getScoreP2()]);
        }


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

    #[Route('/game/{id}/end/{scoreP1}/{scoreP2}', name: 'app_game_end')]
    public function endGame(UserRepository $userRepository,Partie $partie, int $scoreP1, int $scoreP2): Response
    {
        // Récupérer les informations nécessaires pour afficher la page de fin de partie
        $player1 = $partie->getPlayer1();
        $player2 = $partie->getPlayer2();

        // Afficher le score et le gagnant (ou un match nul)
        if ($scoreP1 > $scoreP2) {
            $winner =  $player1;
            $message = "Le joueur " . $winner->getUsername() . " a gagné avec un score de " . $scoreP1 . "!";
        } else if ($scoreP1 < $scoreP2){
            $winner =  $player2;
            $message = "Le joueur " . $winner->getUsername() . " a gagné avec un score de " . $scoreP2 . "!";
        }
        else{
            $message = "La partie s'est terminée par un match nul.";
        }

        $player1->setScoreTotal($scoreP1+$player1->getScoreTotal());
        $userRepository->save($player1, true);
        
        $player2->setScoreTotal($scoreP2+$player2->getScoreTotal());
        $userRepository->save($player2, true);

        // Afficher la page de fin de partie avec le message
        return $this->render('game/end.html.twig', [
            'message' => $message,
        ]);
    }



    #[Route('/partie/{id}/reprendre', name: 'reprendre_partie')]
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
