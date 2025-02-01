<?php

namespace App\Controller\Admin;

use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Bord;
use App\Entity\Filiere;
use App\Repository\ClasseRepository;
use App\Repository\FiliereRepository;
use App\Repository\MatiereRepository;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BordCrudController extends AbstractCrudController
{
    use ReadTrait;
    public static function getEntityFqcn(): string
    {
        return Bord::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile('easyAdminJs/categories.js')
                ->addJsFile('easyAdminJs/collections.js');
        return parent::configureAssets($assets);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['is_published' => 'ASC']);
    }
    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        return [
            AssociationField::new('editor')
                ->setFormTypeOption(
                    'query_builder',
                    function (UserRepository $userRepository) {
                        return $userRepository->createQueryBuilder('u')
                            ->where('u.roles LIKE :admin')
                            ->orWhere('u.roles LIKE :super_admin')
                            ->orWhere('u.roles LIKE :editor')
                            ->setParameter('admin', '%ROLE_ADMIN%')
                            ->setParameter('super_admin', '%ROLE_SUPER_ADMIN%')
                            ->setParameter('editor', '%ROLE_EDITOR%');
                    }
                )->setLabel('Éditeur'),
            TextField::new('title')
                ->setTemplatePath('editor/link_cours.html.twig')
                ->formatValue(function ($value, $entity) {
                    return [
                        'value' => $value,
                        'id' => $entity->getId(),
                        'route' => 'bord_cours'  // Correspond au name de votre route
                    ];
                }),
            //debut des sections
            AssociationField::new('section')
                ->setLabel('Sections')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->setRequired(true),

            //debut des filieres
            AssociationField::new('filiere')
                ->setLabel('Filieres')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->setRequired(false),
            //debut des classes
            AssociationField::new('classe')
                ->setLabel('Classes')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->setRequired(false),
            //debut des matieres
            AssociationField::new('matiere')
                ->setLabel('Matieres')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->setRequired(false),


            AssociationField::new('collection')
                ->setLabel('Collection'),
            TextField::new('small_description')
                ->setLabel('Courte description')
                ->hideOnIndex(),
            TextField::new('keyword')
                ->setLabel('Mots clés')
                ->hideOnIndex(),
            TextEditorField::new('full_description')
                ->setLabel('Longue description')
                ->onlyOnForms(),
            NumberField::new('price')
                ->setLabel('Prix (Fcfa)'),
            NumberField::new('all_user')->onlyOnIndex(),
            NumberField::new('all_gain_bord')
                ->setLabel('Gain Editeur (Fcfa)')
                ->onlyOnIndex(),
            NumberField::new('all_gain_infooxschool')
                ->setLabel('Gain infoox (Fcfa)')
                ->onlyOnIndex(),
            TextField::new('author')->onlyOnForms(),
            BooleanField::new('published'),





        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $bord = new Bord();
        $bord->setStar(0)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastUpdateAt(new \DateTimeImmutable())
            ->setAllUser(0)
            ->setPublished(0)
            ->setAllGainInfooxschool(0)
            ->setAllGainBord(0)
        ;

        return $bord;
    }


    #[Route('/admin/filter-collections', name: 'filter_collections', methods: ['POST'])]
    public function filterCollections(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $editorId = $data['editor'] ?? [];
        $collections = [];
        if($editorId){
            $collections = $userRepository->find($editorId)->getCollectionBords();
        }
        $response = [];
        foreach ($collections as $collection) {
            $response[] = [
                'id' => $collection->getId(),
                'name' => $collection->getTitle(),
            ];
        }
        return new JsonResponse(['collections' => $response]);
    }
}
