<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeController extends AbstractController
{
    #[Route('/me', name: 'app_me')]
    public function index(): Response
    {
        return $this->render('me/index.html.twig', [
            'controller_name' => 'MeController',
        ]);
    }
} 