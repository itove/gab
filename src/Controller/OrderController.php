<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Order;
use App\Entity\Insured;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Allinpay;

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
            'grades' => $grades,
            'school_id' => $schoolId,
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

    #[Route('/order/pending', name: 'app_order_pending')]
    public function pending(Request $request, Allinpay $allinpay): Response
    {
        $sn = $request->query->get('sn');

        $order = $this->doctrine->getRepository(Order::class)->findOneBy(['sn' => $sn]);

        $data = $allinpay->createOrder($sn, $amount);

        // dump($data);
        return $this->render('order/pending.html.twig', ['data' => $data]);
    }

    #[Route('/order/create', name: 'app_order_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Get form data
        $studentName = $request->request->get('student_name');
        $studentIdnum = $request->request->get('student_idnum');
        $schoolId = $request->request->get('school_id');
        $school = $this->doctrine->getRepository(School::class)->find($schoolId);
        $product = $this->doctrine->getRepository(Product::class)->find(1);
        $grade = $request->request->get('grade');
        $class = $request->request->get('class');
        $parentName = $request->request->get('parent_name');
        $parentId = $request->request->get('parent_id');
        $user = $this->getUser();
        $user->setName($parentName);
        $user->setIdnum($parentId);

        try {
            $entityManager = $this->doctrine->getManager();
            
            $insured = $this->doctrine->getRepository(Insured::class)->findOneBy(['idnum' => $studentIdnum]);
            
            if (!$insured) {
                $insured = new Insured();
                $insured->setSchool($school);
                $insured->setIdnum($studentIdnum);
                $insured->setName($studentName);
                $insured->setGrade($grade);
                $insured->setClass($class);
                $entityManager->persist($insured);
            }

            $order = new Order();
            $order->setInsured($insured);
            $order->setApplicant($user);
            $order->setProduct($product);
            $order->setAmount($product->getPrice());
            $entityManager->persist($order);
            $sn = $order->getSn();

            $entityManager->flush();

            // allin pay here

            return new JsonResponse([
                'success' => true,
                'message' => 'Order created successfully',
                'redirectUrl' => $this->generateUrl('app_order_pending', ['sn' => $sn]),
                'sn' => $sn,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 400);
        }
    }

    #[Route('/order/complete', name: 'app_order_complete')]
    public function complete(Request $request): Response
    {
        return $this->render('order/complete.html.twig');
    }
}
