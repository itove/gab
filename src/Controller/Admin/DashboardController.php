<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Insured;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\School;
use App\Entity\Stage;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(OrderCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->renderContentMaximized()
            ->setTitle('关爱保');
    }
    
    public function configureCrud(): Crud
    {
        return Crud::new()
            ->showEntityActionsInlined()
            ->setTimezone('Asia/Shanghai')
            ->setDateTimeFormat('yyyy/MM/dd HH:mm')
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureActions(): Actions
    {
        return Actions::new()
            // ->disable('delete')
            ->add('detail', 'edit')
            ->add('index', 'edit')
            ->add('index', 'new')
            ->add('index', 'delete')
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Insured', 'fas fa-list', Insured::class);
        yield MenuItem::linkToCrud('Order', 'fas fa-list', Order::class);
        yield MenuItem::linkToCrud('Product', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('School', 'fas fa-list', School::class);
        yield MenuItem::linkToCrud('Stage', 'fas fa-list', Stage::class);
        yield MenuItem::linkToCrud('Admin', 'fas fa-list', User::class)
            ->setController(AdminCrudController::class);
        ;
    }
}
