<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Order;
use App\Entity\Insured;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderController extends AbstractController
{
    private $doctrine;
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
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
        $orders = $this->doctrine->getRepository(Order::class)->findBy(['applicant' => $user], ['createdAt' => 'DESC']);
        
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order/create', name: 'app_order_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // Get form data
        $studentName = $request->request->get('student_name');
        $studentIdnum = $request->request->get('student_idnum');
        $schoolId = $request->request->get('school_id');
        $school = $this->doctrine->getRepository(School::class)->find($schoolId);
        $grade = $request->request->get('grade');
        $class = $request->request->get('class');
        $parentName = $request->request->get('parent_name');
        $parentId = $request->request->get('parent_id');

        try {
            // TODO: Add your order creation logic here
            // For example:
            $insured = new Insured();
            $insured->setSchool($school);
            $insured->setIdnum($studentIdnum);
            $insured->setName($studentName);
            $insured->setGrade($grade);
            $insured->setClass($class);
            $doctrine->persist($insured);

            $order = new Order();
            $order->setInsured($insured);
            $order->setApplicant($this->getUser());
            // $order->setProduct());
            $doctrine->persist($order);

            $doctrine->flush();

            $this->addFlash('success', 'Order created successfully');
            return $this->redirectToRoute('app_orders');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to create order: ' . $e->getMessage());
            return $this->redirectToRoute('app_order_new');
        }
    }
} 
