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
        // TODO: Get actual username from user session/authentication
        $username = $this->getUser() ? $this->getUser()->getUserIdentifier() : '游客';

        return $this->render('me/index.html.twig', [
            'username' => $username
        ]);
    }
} 