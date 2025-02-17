<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Refund;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DatetimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Service\Allinpay;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class OrderCrudController extends AbstractCrudController
{
    const ORDER_STATUSES = ['待付款' => 0, '已支付' => 1, '已退款' => 2];

    private $doctrine;
    private AdminUrlGenerator $adminUrlGenerator;
    private Allinpay $allinpay;

    public function __construct(ManagerRegistry $doctrine, AdminUrlGenerator $adminUrlGenerator, Allinpay $allinpay)
    {
        $this->doctrine = $doctrine;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->allinpay = $allinpay;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextField::new('sn');
        yield TextField::new('PaymentSn');
        yield AssociationField::new('applicant');
        yield TextField::new('applicant.idnum');
        yield TextField::new('applicant.phone');
        yield AssociationField::new('insured');
        yield TextField::new('insured.idnum');
        yield TextField::new('insured.school');
        yield TextField::new('insured.grade');
        yield TextField::new('insured.class');
        yield AssociationField::new('product');
        yield MoneyField::new('amount')->setCurrency('CNY');
        yield ChoiceField::new('status')->setChoices(self::ORDER_STATUSES);
        yield DatetimeField::new('createdAt');
        yield DatetimeField::new('PaidAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportXlsx = Action::new('exportXlsx', '导出Excel')
            ->linkToCrudAction('exportXlsx')
            ->createAsGlobalAction();

        $refund = Action::new('refund', '退款')
            ->linkToCrudAction('refund')
            ->displayIf(static function ($entity) {
                return $entity->getStatus() === 1;
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $exportXlsx)
            ->add(Crud::PAGE_INDEX, $refund)
            ->add(Crud::PAGE_DETAIL, $refund)
            ->disable('new')
            ->disable('edit')
            ->disable('delete')
        ;
    }

    public function exportXlsx()
    {
        ini_set('memory_limit', '512M');  // Double memory limit
        ini_set('max_execution_time', 600); // 10 minutes
        
        $batchSize = 100;  // Increase batch size
        $offset = 0;
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['订单号', '通联支付号', '投保人', '投保人身份证', '投保人电话', 
                   '被保人', '被保人身份证', '学校', '年级', '班级', 
                   '产品', '金额', '状态', '创建时间', '支付时间'];
        $sheet->fromArray([$headers], null, 'A1');

        // Set formats
        $sheet->getStyle('D:E')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('L')->getNumberFormat()->setFormatCode('¥#,##0.00');

        // Process in batches
        $row = 2;
        $em = $this->doctrine->getManager();
        $repository = $em->getRepository(Order::class);
        
        // Create query builder for optimization
        $qb = $repository->createQueryBuilder('o')
            ->select('o, a, i, p')  // Remove DISTINCT
            ->leftJoin('o.applicant', 'a')
            ->leftJoin('o.insured', 'i')
            ->leftJoin('o.product', 'p')
            ->andWhere('o.status > :status')
            ->setParameter('status', 0)
            ->groupBy('o.id, a.id, i.id, p.id')  // Add GROUP BY instead of DISTINCT
            ->orderBy('o.id', 'ASC')
            ->setMaxResults($batchSize)
            ->setFirstResult($offset);

        // Add progress tracking
        $totalCount = $repository->createQueryBuilder('o')
            ->select('COUNT(DISTINCT o.id)')  // Use DISTINCT only for count
            ->getQuery()
            ->getSingleScalarResult();

        while ($orders = $qb->getQuery()->getResult()) {
            foreach ($orders as $order) {
                $sheet->fromArray([[
                    $order->getSn(),
                    $order->getPaymentSn(),
                    $order->getApplicant()->getName(),
                    $order->getApplicant()->getIdnum(),
                    $order->getApplicant()->getPhone(),
                    $order->getInsured()->getName(),
                    $order->getInsured()->getIdnum(),
                    $order->getInsured()->getSchool(),
                    $order->getInsured()->getGrade(),
                    $order->getInsured()->getClass(),
                    $order->getProduct()->getName(),
                    $order->getAmount() / 100,
                    array_flip(self::ORDER_STATUSES)[$order->getStatus()],
                    $order->getCreatedAt()->setTimezone(new \DateTimeZone('Asia/Shanghai'))->format('Y-m-d H:i:s'),
                    $order->getPaidAt() ? $order->getPaidAt()->setTimezone(new \DateTimeZone('Asia/Shanghai'))->format('Y-m-d H:i:s') : ''
                ]], null, 'A' . $row);

                $sheet->setCellValueExplicit('B' . $row, $order->getPaymentSn(), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D' . $row, $order->getApplicant()->getIdnum(), DataType::TYPE_STRING);
                // $sheet->setCellValueExplicit('E' . $row, $order->getApplicant()->getPhone(), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('G' . $row, $order->getInsured()->getIdnum(), DataType::TYPE_STRING);

                $row++;

                // Add memory cleanup
                if ($row % 1000 === 0) {
                    $em->clear();
                    gc_collect_cycles();
                }
            }
            
            $offset += $batchSize;
            $qb->setFirstResult($offset);
            $em->clear();
            gc_collect_cycles();
        }

        date_default_timezone_set('Asia/Shanghai');
        // Create the XLSX file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'orders_' . date('Y-m-d_His') . '.xlsx';
        
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );
        $response->headers->set('Content-Disposition', $dispositionHeader);
        
        return $response;
    }

    public function refund(AdminContext $context): Response
    {
        $order = $context->getEntity()->getInstance();
        
        $refund = new Refund();
        $refund->setOrd($order);
        $this->doctrine->getManager()->persist($refund);
        
        try {
            $refundSn = $refund->getSn();
            $result = $this->allinpay->refund(
                $refundSn,
                $order->getPaymentSn(),
                $order->getAmount()
            );

            if (isset($result['trxstatus']) && $result['trxstatus'] === '0000') {
                $order->setStatus(2); // Set to refunded
                $refund->setStatus(1);
                $this->doctrine->getManager()->flush();
                
                $this->addFlash('success', '退款申请成功');
            } else {
                $this->addFlash('error', '退款失败: ' . ($result['retmsg'] ?? '未知错误'));
            }
        } catch (\Exception $e) {
            $this->addFlash('error', '退款出错: ' . $e->getMessage());
        }

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::INDEX)
            ->generateUrl());
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('sn')
            ->add('PaymentSn')
            ->add('applicant')
            // ->add('applicant.idnum')
            //->add('applicant.phone')
            ->add('insured')
            //->add('insured.idnum')
            //->add('insured.school')
            ->add(ChoiceFilter::new('status')->setChoices(self::ORDER_STATUSES))
            ->add('createdAt')
            ->add('PaidAt')
        ;
    }
}
