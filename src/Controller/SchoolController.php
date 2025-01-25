<?php

namespace App\Controller;

use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchoolController extends AbstractController
{
    #[Route('/school', name: 'app_school')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $schools = $entityManager->getRepository(School::class)->findAll();

        return $this->render('school/index.html.twig', [
            'schools' => $schools,
        ]);
    }
} 