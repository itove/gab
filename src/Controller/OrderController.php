<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order/new', name: 'app_order_new')]
    public function new(): Response
    {
        $order = new Order();
        
        return $this->render('order/new.html.twig', [
            'order' => $order,
        ]);
    }
} 