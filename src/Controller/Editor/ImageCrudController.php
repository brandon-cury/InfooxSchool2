<?php

namespace App\Controller\Editor;

use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Epreuve;
use App\Entity\Image;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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


class ImageCrudController extends AbstractCrudController
{
    use ReadTrait;
    private $requestStack;
    private $adminUrlGenerator;
    private $bord;

    private $uploadDirImage;
    public function __construct(RequestStack $requestStack, BordRepository $bordRepository, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->requestStack = $requestStack->getCurrentRequest();
        $this->adminUrlGenerator = $adminUrlGenerator->setDashboard(EditorDashboardController::class);

        $bordId = $this->requestStack->get('bordId');
        $this->bord = $bordRepository->find($bordId);

        $this->uploadDirImage = 'MauvaiseDirImage/';

        if ($this->bord) {
            $this->uploadDirImage = 'bords/' . $this->bord->getPath() . '/images';
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->uploadDirImage)) {
                $filesystem->mkdir($this->uploadDirImage, 0700);
            }
        }
    }
    public static function getEntityFqcn(): string
    {
        return Image::class;
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
            ->setPageTitle(Crud::PAGE_INDEX, 'Images du livre : ' . $this->bord->getTitle())
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une Image au livre : ' . $this->bord->getTitle())
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer l\'image du livre : ' . $this->bord->getTitle())
            ->setDefaultSort(['sort' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            //debut de l'image
            ImageField::new('path')
                ->setLabel('Image')
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
        $addImageUrl = $this->adminUrlGenerator->setController(ImageCrudController::class)
            ->setAction('new')
            ->set('bordId', $bordId)
            ->generateUrl();

        return $actions
            // Met à jour l'action 'new' pour inclure bordId
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) use ($addImageUrl) {
                return $action->linkToUrl($addImageUrl);
            })


            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) use ($bordId) {
                return $action->linkToUrl(function (Image $entity) use ($bordId) {
                    return $this->adminUrlGenerator
                        ->setController(ImageCrudController::class)
                        ->setAction('edit')
                        ->setEntityId($entity->getId()) // Inclure entityId
                        ->set('bordId', $bordId)
                        ->generateUrl();
                });
            });

    }
    public function createEntity(string $entityFqcn)
    {
        $image = new Image();
        $image->setBord($this->bord);
        $nextSortValue = count($this->bord->getImages()) + 1;
        $image->setSort($nextSortValue);
        return $image;
    }


}
