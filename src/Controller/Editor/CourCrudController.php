<?php

namespace App\Controller\Editor;

use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Bord;
use App\Entity\Cour;
use App\Entity\Epreuve;
use App\Repository\BordRepository;
use App\Repository\CourRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\File;


class CourCrudController extends AbstractCrudController
{
    use ReadTrait;
    private $requestStack;
    private $adminUrlGenerator;
    private $bord;

    private $uploadDirDocument;
    private $uploadDirImage;

    public function __construct(RequestStack $requestStack, BordRepository $bordRepository, CourRepository $courRepository, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->requestStack = $requestStack->getCurrentRequest();
        $this->adminUrlGenerator = $adminUrlGenerator->setDashboard(EditorDashboardController::class);

        if($this->requestStack->get('bordId')){
            $bordId = $this->requestStack->get('bordId');
            $this->bord = $bordRepository->find($bordId);
        }elseif ($this->requestStack->get('courId')){
            $courId = $this->requestStack->get('courId');
            $this->bord = $courRepository->find($courId)->getBord();
        }


        // creation des chemin et dossiers
        $this->uploadDirDocument = 'MauvaiseDirDocument/';
        $this->uploadDirImage = 'MauvaiseDirImage/';

        if ($this->bord) {
            $this->uploadDirDocument = 'bords/' . $this->bord->getPath() . '/documents';
            $this->uploadDirImage = 'bords/' . $this->bord->getPath() . '/images';

            // Utilisation de Filesystem pour créer le répertoire s'il n'existe pas
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->uploadDirDocument)) {
                $filesystem->mkdir($this->uploadDirDocument, 0700);
            }
            if (!$filesystem->exists($this->uploadDirImage)) {
                $filesystem->mkdir($this->uploadDirImage, 0700);
            }
        }
    }
    public static function getEntityFqcn(): string
    {
        return Cour::class;
    }

    #[Route('/editor/cour/{id}/exercices', name: 'cour_exercices')]
    public function showExercices(Cour $cour): Response
    {
        return $this->redirect(
            $this->adminUrlGenerator->setController(ExerciceCrudController::class)
                ->setAction('index')
                ->set('courId', $cour->getId())
                ->generateUrl()
        );
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if($this->bord->getEditor()->getId() != $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN') ) {
            throw new AccessDeniedException('Vous n\'avez pas accès à ce livre.');
        }

        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $bordId = $this->bord->getId();

        if ($bordId) {
            $qb->andWhere('entity.bord = :bordId')
                ->setParameter('bordId', $bordId);
        }
        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $bordTitle = $this->bord->getTitle();

        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Cours du livre : ' . $bordTitle)
            ->setPageTitle(Crud::PAGE_NEW, 'Nouveau cour du livre : ' . $bordTitle)
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer le cour du livre : ' . $bordTitle)
            ->setDefaultSort(['sort' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')
                ->setTemplatePath('editor/link_exercices.html.twig')
                ->formatValue(function ($value, $entity) {
                    return [
                        'value' => $value,
                        'id' => $entity->getId(),
                        'route' => 'cour_exercices'  // Correspond au name de votre route
                    ];
                }),
            //debut du cour
            ImageField::new('content')
                ->hideOnIndex()
                ->hideOnDetail()
                ->setLabel('content')
                ->setBasePath('content')
                ->setUploadDir('public/' . $this->uploadDirDocument)
                ->setFileConstraints(new File([
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        'application/msword',
                        'application/vnd.ms-excel'
                    ],
                    'mimeTypesMessage' => 'Documento no válido, solo se permiten PDF, DOC o XLS',
                ])),
            UrlField::new('content')
                ->setLabel('content')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if($value){
                        $url = '/' . $this->uploadDirDocument . '/' . $entity->getContent();
                        return sprintf('<a href="%s" target="_blank">Télécharger</a>', $url);
                    } ;
                    return null;
                }),
            TextEditorField::new('content')
                ->setLabel('content')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    if($value){
                        $url = '/' . $this->uploadDirDocument . '/' . $entity->getContent();
                        return sprintf('<a href="%s" target="_blank">Télécharger</a>', $url);
                    };
                }),
            //fin de l'epreuve

            TextField::new('video_link'),

            //debut de l'image de la video
            ImageField::new('video_img')
                ->setLabel('video_img')
                ->setBasePath($this->uploadDirImage)
                ->setUploadDir('public/' . $this->uploadDirImage)
                ->setUploadedFileNamePattern('[name]_[timestamp].[extension]')
                ->setFileConstraints(new File([
                    //'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)', ])),
            //fin de l'image de la video

            NumberField::new('sort')->hideOnForm(),


        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $bordId = $this->bord->getId();
        $addEpreuveUrl = $this->adminUrlGenerator->setController(CourCrudController::class)
            ->setAction('new')
            ->set('bordId', $bordId)
            ->generateUrl();

        return $actions
            // Met à jour l'action 'new' pour inclure bordId
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) use ($addEpreuveUrl) {
                return $action->linkToUrl($addEpreuveUrl);
            })


            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) use ($bordId) {
                return $action->linkToUrl(function (Cour $entity) use ($bordId) {
                    return $this->adminUrlGenerator
                        ->setController(CourCrudController::class)
                        ->setAction('edit')
                        ->setEntityId($entity->getId()) // Inclure entityId
                        ->set('bordId', $bordId)
                        ->generateUrl();
                });
            });

    }

    public function createEntity(string $entityFqcn)
    {
        $cour = new Cour();

        // Récupérer l'ID du Bord à partir de la requête
        $bord = $this->bord;
        $nextSortValue = count($bord->getEpreuves()) + 1;
        $cour->setBord($bord)
                ->setSort($nextSortValue)
            ;

        return $cour;
    }

}
