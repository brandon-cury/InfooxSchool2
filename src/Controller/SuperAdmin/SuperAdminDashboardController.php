<?php

namespace App\Controller\SuperAdmin;

use App\Controller\Admin\BordCrudController;
use App\Controller\Admin\CollectionBordCrudController;
use App\Controller\Admin\CommentCrudController;
use App\Controller\Admin\MoneyWithdrawalCrudController;
use App\Entity\Classe;
use App\Entity\CollectionBord;
use App\Entity\Comment;
use App\Entity\Examen;
use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Entity\MoneyWithdrawal;
use App\Entity\Section;
use App\Entity\User;
use App\Entity\UserBord;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SuperAdminDashboardController extends AbstractDashboardController
{
    #[Route('/super-admin', name: 'super_admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(BordCrudController::class)->generateUrl());

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
            ->setTitle('SUPER ADMIN - InfooxSchool');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToDashboard('Tous les Bords', 'fa fa-home');
        yield MenuItem::linkToUrl('Mes propres Livres', 'fas fa-arrow-right', $this->generateUrl('editor'));
        yield MenuItem::linkToCrud('utilisateurs', 'fas fa-users', User::class)
            ->setController(UserCrudController::class);
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', Comment::class)
            ->setController(CommentCrudController::class);
        yield MenuItem::linkToCrud('Inscriptions', 'fas fa-book', UserBord::class);
        yield MenuItem::linkToCrud('Examens', 'fas fa-list', Examen::class);
        yield MenuItem::linkToCrud('Sections', 'fas fa-list', Section::class);
        yield MenuItem::linkToCrud('Filières', 'fas fa-list', Filiere::class);
        yield MenuItem::linkToCrud('Classes', 'fas fa-list', Classe::class);
        yield MenuItem::linkToCrud('Matières', 'fas fa-list', Matiere::class);
        yield MenuItem::linkToCrud(' Collections', 'fas fa-list', CollectionBord::class)
            ->setController(CollectionBordCrudController::class);
        yield MenuItem::linkToCrud('Retrait des Gains', 'fas fa-usd', MoneyWithdrawal::class)
            ->setController(MoneyWithdrawalCrudController::class);
        yield MenuItem::linkToCrud('Toute l\'équipe', 'fas fa-users', User::class)
            ->setController(User2CrudController::class);
        yield MenuItem::linkToCrud('Tous les éditeurs', 'fas fa-users', User::class)
            ->setController(User3CrudController::class);
    }
}
