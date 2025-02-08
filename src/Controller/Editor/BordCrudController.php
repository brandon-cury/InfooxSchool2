<?php

namespace App\Controller\Editor;


use App\Controller\Editor\Trait\ReadTrait;
use App\Entity\Bord;
use App\Repository\ClasseRepository;
use App\Repository\CollectionBordRepository;
use App\Repository\FiliereRepository;
use App\Repository\SectionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class BordCrudController extends AbstractCrudController
{
    use ReadTrait;

    public static function getEntityFqcn(): string
    {
        return Bord::class;
    }

    #[Route('/editor/book/{id}/cours', name: 'bord_cours')]
    public function showCours(Bord $bord): Response
    {

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect(
            $adminUrlGenerator->setDashboard(EditorDashboardController::class) // Spécifiez votre tableau de bord ici
            ->setController(CourCrudController::class)
                ->setAction('index')
                ->set('bordId', $bord->getId())
                ->generateUrl()
        );
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $user = $this->getUser();
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.editor = :user')
            ->setParameter('user', $user);
        return $qb;
    }

    public function configureAssets(Assets $assets): Assets
    {
        // Vérifiez si nous sommes sur la page de création

            $assets->addJsFile('easyAdminJs/categories.js');


        return parent::configureAssets($assets);
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        return [
            ImageField::new('imageP', 'Image(P)')
                ->onlyOnIndex(),
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
                ->setLabel('Collection')
                ->setFormTypeOptions([
                    'query_builder' => function (CollectionBordRepository $er) use ($user) {
                        return $er->createQueryBuilder('c')
                            ->where('c.editor = :user')
                            ->setParameter('user', $user);
                    },
                ]),

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
            NumberField::new('star')->onlyOnIndex(),
            NumberField::new('all_gain_bord')
                ->setLabel('Ventes (Fcfa)')
                ->onlyOnIndex(),
            DateTimeField::new('last_update_at')->onlyOnIndex(),
            NumberField::new('numb_page'),
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
            ->setEditor($this->getUser())
            ->setAllGainInfooxschool(0)
            ->setAllGainBord(0)

        ;

        return $bord;
    }



    #[Route('/filter-filieres', name: 'filter_filieres', methods: ['POST'])]
    public function filterFilieres(Request $request, SectionRepository $sectionRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $sections = $data['sections'] ?? [];
        $filieres = [];
        foreach ($sections as $section){
            $filieres = array_merge($filieres, $sectionRepository->find($section)->getFilieres()->toArray());
        }
        $result = [];
        foreach ($filieres as $filiere) {
            $result[] = ['id' => $filiere->getId(), 'name' => $filiere->getTitle()];
        }
        return new JsonResponse(['filieres' => $result]);
    }

    #[Route('/filter-classes', name: 'filter_classes', methods: ['POST'])]
    public function filterClasses(Request $request, FiliereRepository $filiereRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $filieres = $data['filieres'] ?? [];
        $classes = [];

        foreach ($filieres as $filiere) {
            $filiereEntity = $filiereRepository->find($filiere);
            if ($filiereEntity) {
                $classes = array_merge($classes, $filiereEntity->getClasses()->toArray());
            }
        }

        $resultClasses = [];
        foreach ($classes as $classe) {
            $resultClasses[] = ['id' => $classe->getId(), 'name' => $classe->getTitle()];
        }

        return new JsonResponse(['classes' => $resultClasses]);
    }

    #[Route('/filter-matieres', name: 'filter_matieres', methods: ['POST'])]
    public function filterMatieres(Request $request, ClasseRepository $classeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $classes = $data['classes'] ?? [];
        $matieres = [];

        foreach ($classes as $classe) {
            $classeEntity = $classeRepository->find($classe);
            if ($classeEntity) {
                $matieres = array_merge($matieres, $classeEntity->getMatieres()->toArray());
            }
        }

        $resultMatieres = [];
        foreach ($matieres as $matiere) {
            $resultMatieres[] = ['id' => $matiere->getId(), 'name' => $matiere->getTitle()];
        }

        return new JsonResponse(['matieres' => $resultMatieres]);
    }

}
