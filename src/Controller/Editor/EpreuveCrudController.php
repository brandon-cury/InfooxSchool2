<?php

namespace App\Controller\Editor;

use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Epreuve;
use App\Repository\BordRepository;
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

class EpreuveCrudController extends AbstractCrudController
{
    use ReadTrait;
    private $requestStack;
    private $adminUrlGenerator;
    private $bord;

    private $uploadDirDocument;
    private $uploadDirImage;

    public function __construct(RequestStack $requestStack, BordRepository $bordRepository, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->requestStack = $requestStack->getCurrentRequest();
        $this->adminUrlGenerator = $adminUrlGenerator->setDashboard(EditorDashboardController::class);

        $bordId = $this->requestStack->get('bordId');
        $this->bord = $bordRepository->find($bordId);

        // creation des chemin et dossiers
        $this->uploadDirDocument = 'MauvaiseDirDocument/';
        $this->uploadDirImage = 'MauvaiseDirImage/';

        if ($this->bord) {
            $this->uploadDirDocument = 'bords/' . $this->bord->getPath() . '/documents';
            $this->uploadDirImage = 'bords/' . $this->bord->getPath() . '/images';

            // Utilisation de Filesystem pour créer le répertoire s'il n'existe pas
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->uploadDirDocument)) {
                $filesystem->mkdir($this->uploadDirDocument, 0755);
            }
            if (!$filesystem->exists($this->uploadDirImage)) {
                $filesystem->mkdir($this->uploadDirImage, 0755);
            }
        }
    }
    public static function getEntityFqcn(): string
    {
        return Epreuve::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if($this->bord->getEditor()->getId() != $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN') ) {
            throw new AccessDeniedException('Vous n\'avez pas accès à ce livre.');
        }

        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $request = $this->requestStack;
        $bordId = $request->query->get('bordId');

        if ($bordId) {
            $qb->andWhere('entity.bord = :bordId')
                ->setParameter('bordId', $bordId);
        }

        return $qb;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Epreuves du livre : ' . $this->bord->getTitle())
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter épreuve du livre : ' . $this->bord->getTitle())
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer l\'épreuve du livre : ' . $this->bord->getTitle())
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

            NumberField::new('sort'),


        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $bordId = $this->bord->getId();
        $addEpreuveUrl = $this->adminUrlGenerator->setController(EpreuveCrudController::class)
            ->setAction('new')
            ->set('bordId', $bordId)
            ->generateUrl();

        return $actions
            // Met à jour l'action 'new' pour inclure bordId
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) use ($addEpreuveUrl) {
                return $action->linkToUrl($addEpreuveUrl);
            })


            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) use ($bordId) {
                return $action->linkToUrl(function (Epreuve $entity) use ($bordId) {
                    return $this->adminUrlGenerator
                        ->setController(EpreuveCrudController::class)
                        ->setAction('edit')
                        ->setEntityId($entity->getId()) // Inclure entityId
                        ->set('bordId', $bordId)
                        ->generateUrl();
                });
            });

    }
    public function createEntity(string $entityFqcn)
    {
        $epreuve = new Epreuve();
        $bord = $this->bord;

        $epreuve->setStar(0)
            ->setUpdateAt(new \DateTimeImmutable())
            ->setUpdateAt(new \DateTimeImmutable())
            ->setAllUser(0)
            ->setOnligne(1)
            ->setEditor($bord->getEditor());


        // Récupérer l'ID du Bord à partir de la requête
        if ($bord) {
            $nextSortValue = count($bord->getEpreuves()) + 1;
            $epreuve->setBord($bord)
                ->setSort($nextSortValue)
            ;
        }
        return $epreuve;
    }

}
