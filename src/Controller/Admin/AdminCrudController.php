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
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class AdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.isAdmin = :isAdmin')
           ->setParameter('isAdmin', true);
        
        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('username');
        yield TextField::new('name');
        yield TextField::new('plainPassword')
            ->onlyOnForms()
        ;
        // yield TextField::new('phone');
        // yield TextField::new('idnum');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->disable('new')
            // ->disable('edit')
            // ->disable('delete')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('管理员')
            ->setEntityLabelInPlural('管理员')
            // ->setPageTitle('new', '新建管理员')
        ;
    }

    public function createEntity(string $entityFqcn)
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        return $user;
    }
}
