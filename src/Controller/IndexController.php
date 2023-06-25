<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_redirect')]
    public function indexRedirect(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/index', name: 'app_index')]
    public function index(UserRepository $userRepository) : Response {
        $users = $userRepository->findAll();
        return $this->render('index/index.html.twig',[
            'users' => $users
        ]);
    }
}
