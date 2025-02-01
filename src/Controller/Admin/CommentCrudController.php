<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['is_published' => 'ASC']);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('user.avatar') // Assurez-vous d'utiliser le bon chemin d'accès à la propriété de l'image de l'utilisateur
            ->setBasePath('/avatar/')
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
