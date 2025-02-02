<?php

namespace App\Controller\Editor;


use App\Entity\Bord;
use App\Entity\CollectionBord;
use App\Entity\Comment;
use App\Entity\Cour;
use App\Entity\Epreuve;
use App\Entity\Image;
use App\Entity\MoneyWithdrawal;
use App\Repository\CourRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditorDashboardController extends AbstractDashboardController
{
    private $requestStack;
    private $courRepository;
    public function __construct(RequestStack $requestStack, CourRepository $courRepository)
    {
        $this->requestStack = $requestStack;
        $this->courRepository = $courRepository;
    }

    #[Route('/editor', name: 'editor')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend

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
            ->setTitle('EDITOR - InfooxSchool');
    }

    public function configureMenuItems(): iterable
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToUrl('Retour au Super Admin', 'fas fa-arrow-left', $this->generateUrl('super_admin'));
        }
        elseif ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToUrl('Retour à l\'admin', 'fas fa-arrow-left', $this->generateUrl('admin'));
        }


        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Mes Livres', 'fas fa-book', Bord::class)->setController(BordCrudController::class);
        yield MenuItem::linkToCrud('Mes Collections', 'fas fa-list', CollectionBord::class)->setController(CollectionBordCrudController::class);
        yield MenuItem::linkToCrud('Tous Les Commentaires', 'fas fa-comment', Comment::class)->setController(CommentCrudController::class);
        yield MenuItem::linkToCrud('Gains Retirés', 'fas fa-usd', MoneyWithdrawal::class)->setController(MoneyWithdrawalCrudController::class);

        $request = $this->requestStack->getCurrentRequest();
        $bordId = $request->query->get('bordId');
        $courId = $request->query->get('courId');

        // Vérifiez si nous sommes sur la page des cours ou des épreuves pour un Bord spécifique
        if ($bordId) {
            yield MenuItem::section('Spécifique au Bord');
            yield MenuItem::linkToCrud('Cours', 'fa fa-list', Cour::class)->setQueryParameter('bordId', $bordId)->setController(CourCrudController::class);
            yield MenuItem::linkToCrud('Épreuves', 'fa fa-file', Epreuve::class)->setQueryParameter('bordId', $bordId)->setController(EpreuveCrudController::class);
            yield MenuItem::linkToCrud('Images', 'fa fa-image', Image::class)->setQueryParameter('bordId', $bordId)->setController(ImageCrudController::class);
        }
        // Vérifiez si nous sommes sur la page des cours ou des épreuves pour un Bord spécifique
        if ($courId) {
            yield MenuItem::section('Spécifique au Cour');
            yield MenuItem::linkToCrud('Cours', 'fa fa-list', Cour::class)->setQueryParameter('bordId', $this->courRepository->find($courId)->getBord()->getId())->setController(CourCrudController::class);
        }
    }
}
