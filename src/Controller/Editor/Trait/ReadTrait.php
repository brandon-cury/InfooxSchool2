<?php
namespace App\Controller\Editor\Trait;


use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

trait ReadTrait
{
    public function configureActions(Actions $actions): Actions
    {
        $actions->disable( Action::DELETE)
                ->add(Crud::PAGE_INDEX, Action::DETAIL);
        return $actions;
    }
}