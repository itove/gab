<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class UserCrudController extends AbstractCrudController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.isAdmin = :isAdmin')
           ->setParameter('isAdmin', false);
        
        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('username');
        yield TextField::new('name');
        yield TextField::new('phone');
        yield TextField::new('idnum');
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('phone')
            ->add('idnum')
        ;
    }

    public function exportXlsx()
    {
        $userRepository = $this->doctrine->getRepository(User::class);
        $users = $userRepository->findBy(['isAdmin' => false]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['ID', '用户名', '姓名', '电话', '身份证'];
        $sheet->fromArray([$headers], null, 'A1');

        // Set text format for ID and phone columns
        $sheet->getStyle('D:E')->getNumberFormat()->setFormatCode('@');

        // Add data rows
        $row = 2;
        foreach ($users as $user) {
            $sheet->fromArray([[
                $user->getId(),
                $user->getUsername(),
                $user->getName(),
                $user->getPhone(),
                $user->getIdnum()
            ]], null, 'A' . $row);

            // Set explicit string type for ID numbers and phone
            $sheet->setCellValueExplicit('D' . $row, $user->getPhone(), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, $user->getIdnum(), DataType::TYPE_STRING);
            
            $row++;
        }

        date_default_timezone_set('Asia/Shanghai');
        // Create response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'users_' . date('Y-m-d_His') . '.xlsx';
        
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
