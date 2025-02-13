<?php

namespace App\Controller\Admin;

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
use Doctrine\Persistence\ManagerRegistry;

class RefundCrudController extends AbstractCrudController
{
    const ORDER_STATUSES = ['待处理' => 0, '已退款' => 1];

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getEntityFqcn(): string
    {
        return Refund::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id');
        yield TextField::new('sn');
        yield AssociationField::new('ord');
        yield ChoiceField::new('status')->setChoices(self::ORDER_STATUSES);
        yield DatetimeField::new('createdAt');
        yield DatetimeField::new('PaidAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new')
            ->disable('edit')
            ->disable('delete')
        ;
    }
}
