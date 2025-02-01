<?php

namespace App\Controller\Editor;

use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Comment;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentCrudController extends AbstractCrudController
{
    use ReadTrait;
    private $requestStack;
    private $adminUrlGenerator;
    private $bord;
    public function __construct(RequestStack $requestStack, BordRepository $bordRepository, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->requestStack = $requestStack->getCurrentRequest();
        $this->adminUrlGenerator = $adminUrlGenerator->setDashboard(EditorDashboardController::class);

        $bordId = $this->requestStack->get('bordId');
        if($bordId) $this->bord = $bordRepository->find($bordId);

    }
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {


        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $request = $this->requestStack;
        $bordId = $request->query->get('bordId');
        $commentId = $request->query->get('commentId');

        $user = $this->getUser();
        if($commentId){
            return $qb->andWhere('entity.id = :commentId')
                ->setParameter('commentId', $commentId);
        }
        return $qb->innerJoin('entity.bord', 'b')
                ->where('b.editor = :user')
                ->setParameter('user', $user);


    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['is_published' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('user.avatar') // Assurez-vous d'utiliser le bon chemin d'accès à la propriété de l'image de l'utilisateur
            ->setBasePath('/images/avatar/')
                ->setLabel('User')
                ->onlyOnIndex(),
            TextField::new('content')
                ->renderAsHtml()
                ->onlyOnIndex(),
            TextField::new('bord.title')
                ->setLabel('livre')
                ->onlyOnIndex(),
            BooleanField::new('published'),
            NumberField::new('rating')
                ->onlyOnIndex(),
            BooleanField::new('send')
                ->setLabel('Demander une modification')

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE);
    }

}
