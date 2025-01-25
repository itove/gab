<?php

namespace App\Controller;

use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;

class OrderController extends AbstractController
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    #[Route('/order/new', name: 'app_order_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolId = $request->query->get('schoolId');
        $grades = [];
        
        if ($schoolId) {
            $school = $entityManager->getRepository(School::class)->find($schoolId);
            if ($school) {
                foreach ($school->getStage() as $stage) {
                    foreach ($stage->getGrades() as $index => $gradeName) {
                        $grades[] = [
                            'id' => $index + 1,
                            'value' => $gradeName
                        ];
                    }
                }
            }
        }

        return $this->render('order/new.html.twig', [
            'grades' => $grades
        ]);
    }

    #[Route('/orders', name: 'app_orders')]
    public function index(): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // Get orders for the current user as applicant
        $orders = $this->orderRepository->findBy(['applicant' => $user], ['createdAt' => 'DESC']);
        
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }
} 
