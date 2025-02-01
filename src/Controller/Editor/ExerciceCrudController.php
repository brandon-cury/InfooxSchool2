<?php

namespace App\Controller\Editor;

use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Exercice;
use App\Repository\CourRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\File;

class ExerciceCrudController extends AbstractCrudController
{
    use ReadTrait;
    private $requestStack;
    private $adminUrlGenerator;
    private $cour;

    private $uploadDirDocument;
    private $uploadDirImage;

    public function __construct(RequestStack $requestStack, CourRepository $courRepository, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->requestStack = $requestStack->getCurrentRequest();
        $this->adminUrlGenerator = $adminUrlGenerator->setDashboard(EditorDashboardController::class);

        $courId = $this->requestStack->get('courId');
        $this->cour = $courRepository->find($courId);

        // creation des chemin et dossiers
        $this->uploadDirDocument = 'MauvaiseDirBocument/';
        $this->uploadDirImage = 'MauvaiseDirImage/';

        if ($this->cour) {
            $this->uploadDirDocument = 'bords/' . $this->cour->getBord()->getPath() . '/documents';
            $this->uploadDirImage = 'bords/' . $this->cour->getBord()->getPath() . '/images';

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
        return Exercice::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if($this->cour->getBord()->getEditor()->getId() != $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN') ) {
            throw new AccessDeniedException('Vous n\'avez pas accès à ce livre.');
        }

        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $request = $this->requestStack;

        if ($this->cour) {
            $qb->andWhere('entity.cour = :courId')
                ->setParameter('courId', $this->cour->getId());
        }

        return $qb;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Exercices du cours : ' . $this->cour->getTitle())
            ->setPageTitle(Crud::PAGE_NEW, 'Nouveau exercice du cours : ' . $this->cour->getTitle())
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer l\'exercice du cours : ' . $this->cour->getTitle())
            ->setDefaultSort(['sort' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            //debut de l'epreuve
            ImageField::new('content')
                ->hideOnIndex()
                ->hideOnDetail()
                ->setLabel('content')
                ->setBasePath('epreuve')
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

            //debut de du corrige
            ImageField::new('corrected')
                ->hideOnIndex()
                ->hideOnDetail()
                ->setLabel('corrected')
                ->setBasePath('corrected')
                ->setUploadDir('public/' . $this->uploadDirDocument)
                ->setUploadedFileNamePattern('[name]_[timestamp].[extension]')
                ->setFileConstraints(new File([
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        'application/msword',
                        'application/vnd.ms-excel'
                    ],
                    'mimeTypesMessage' => 'Documento no válido, solo se permiten PDF, DOC o XLS',
                ])),
            UrlField::new('corrected')
                ->setLabel('corrected')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if($value){
                        $url = '/' . $this->uploadDirDocument . '/' . $entity->getCorrected();
                        return sprintf('<a href="%s" target="_blank">Télécharger</a>', $url);
                    } ;
                    return null;
                }),
            TextEditorField::new('corrected')
                ->setLabel('corrected')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    if($value){
                        $url = '/' . $this->uploadDirDocument . '/' . $entity->getCorrected();
                        return sprintf('<a href="%s" target="_blank">Télécharger</a>', $url);
                    };
                }),
            //fin du corrigé

            TextField::new('video_link'),

            //debut de l'image de la video
            ImageField::new('video_img')
                ->setLabel('video_img')
                ->setBasePath($this->uploadDirImage)
                ->setUploadDir('public/' . $this->uploadDirImage)
                ->setUploadedFileNamePattern('[name]_[timestamp].[extension]')
                ->setFileConstraints(new File([
                    //'maxSize' => '2M',
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
        $courId = $this->cour->getId();
        $addExerciceUrl = $this->adminUrlGenerator->setController(ExerciceCrudController::class)
            ->setAction('new')
            ->set('courId', $courId)
            ->generateUrl();

        return $actions
            // Met à jour l'action 'new' pour inclure bordId
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) use ($addExerciceUrl) {
                return $action->linkToUrl($addExerciceUrl);
            })


            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) use ($courId) {
                return $action->linkToUrl(function (Exercice $entity) use ($courId) {
                    return $this->adminUrlGenerator
                        ->setController(ExerciceCrudController::class)
                        ->setAction('edit')
                        ->setEntityId($entity->getId()) // Inclure entityId
                        ->set('courId', $courId)
                        ->generateUrl();
                });
            });

    }

    public function createEntity(string $entityFqcn)
    {
        $exercice = new Exercice();
        $exercice->setEditor($this->cour->getBord()->getEditor())
            ->setCour($this->cour);

        $nextSortValue = count($this->cour->getExercices()) + 1;
        $exercice->setSort($nextSortValue);

        return $exercice;
    }
}
