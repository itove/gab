<?php

namespace App\Controller\Admin;

use App\Entity\Order;
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

class OrderCrudController extends AbstractCrudController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    const ORDER_STATUSES = ['待付款' => 0, '已支付' => 1];

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

        return $actions
            ->add(Crud::PAGE_INDEX, $exportXlsx)
            ->disable('new')
            ->disable('edit')
            ->disable('delete')
        ;
    }

    public function exportXlsx()
    {
        $orderRepository = $this->doctrine->getRepository(Order::class);
        $orders = $orderRepository->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['订单号', '通联支付号', '投保人', '投保人身份证', '投保人电话', 
                   '被保人', '被保人身份证', '学校', '年级', '班级', 
                   '产品', '金额', '状态', '创建时间', '支付时间'];
        $sheet->fromArray([$headers], null, 'A1');

        // Set text format for ID and phone columns
        $sheet->getStyle('D:E')->getNumberFormat()->setFormatCode('@');  // 投保人身份证和电话
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode('@');    // 被保人身份证
        
        // Set currency format for amount column
        $sheet->getStyle('L')->getNumberFormat()->setFormatCode('¥#,##0.00');

        // Add data rows
        $row = 2;
        foreach ($orders as $order) {
            $createdAt = clone $order->getCreatedAt();
            $createdAt->setTimezone(new \DateTimeZone('Asia/Shanghai'));
            
            $paidAt = null;
            if ($order->getPaidAt()) {
                $paidAt = clone $order->getPaidAt();
                $paidAt->setTimezone(new \DateTimeZone('Asia/Shanghai'));
            }
            
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
                $createdAt->format('Y-m-d H:i:s'),
                $paidAt ? $paidAt->format('Y-m-d H:i:s') : ''
            ]], null, 'A' . $row);
            $row++;
        }

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
