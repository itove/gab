<?php

namespace App\Controller\Admin;

use App\Entity\Insured;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\Persistence\ManagerRegistry;

class InsuredCrudController extends AbstractCrudController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getEntityFqcn(): string
    {
        return Insured::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextField::new('name');
        yield TextField::new('idnum');
        yield AssociationField::new('school');
        yield TextField::new('grade');
        yield TextField::new('class');
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
            ->disable('delete');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('idnum')
            ->add('school');
    }

    public function exportXlsx()
    {
        $insuredRepository = $this->doctrine->getRepository(Insured::class);
        $insureds = $insuredRepository->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['ID', '姓名', '身份证', '学校', '年级', '班级'];
        $sheet->fromArray([$headers], null, 'A1');

        // Set text format for ID numbers
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode('@');

        // Add data rows
        $row = 2;
        foreach ($insureds as $insured) {
            $sheet->fromArray([[
                $insured->getId(),
                $insured->getName(),
                $insured->getIdnum(),
                $insured->getSchool(),
                $insured->getGrade(),
                $insured->getClass()
            ]], null, 'A' . $row);

            // Set explicit string type for ID number
            $sheet->setCellValueExplicit('C' . $row, $insured->getIdnum(), DataType::TYPE_STRING);
            
            $row++;
        }

        date_default_timezone_set('Asia/Shanghai');
        // Create response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'insureds_' . date('Y-m-d_His') . '.xlsx';
        
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
}
