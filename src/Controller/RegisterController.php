<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    /*
    #[Route('/register/step1', name: 'register_step1')]
    public function step1(): Response
    {
        // Rendre le template de l'étape 1
        return $this->render('register/step1.html.twig');
    }

    #[Route('/register/step2', name: 'register_step2')]
    public function step2(): Response
    {
        // Rendre le template de l'étape 2
        return $this->render('register/step2.html.twig');
    }*/
}
