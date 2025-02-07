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

class OrderCrudController extends AbstractCrudController
{
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
        yield ChoiceField::new('status')->setChoices(['待付款' => 0, '已支付' => 1]);
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
