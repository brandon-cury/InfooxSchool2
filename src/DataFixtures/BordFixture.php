<?php

namespace App\DataFixtures;

use App\Entity\Bord;
use App\Entity\Classe;
use App\Entity\CollectionBord;
use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Entity\Section;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BordFixture extends Fixture implements DependentFixtureInterface
{
    private array $users;
    private array $collectionBords;

    private array $sections;

    private array $classes;

    private array $filieres;

    private array $matieres;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $this->users = $manager->getRepository(User::class)->findAll();
        $this->collectionBords = $manager->getRepository(CollectionBord::class)->findAll();
        $this->sections = $manager->getRepository(Section::class)->findAll();
        $this->classes = $manager->getRepository(Classe::class)->findAll();
        $this->filieres = $manager->getRepository(Filiere::class)->findAll();
        $this->matieres = $manager->getRepository(Matiere::class)->findAll();
        foreach ($this->users as $user) {
            if(!empty($user->getCollectionBords())){
                for ($i = 1; $i <= $faker->numberBetween(0, 5); $i++) {
                    $bord = new Bord();
                    $section_choisie = $faker->randomElement($this->sections);
                    $filiere_choisie = $faker->randomElement($section_choisie->getFilieres());
                    $classe_choisie = $faker->randomElement($filiere_choisie->getClasses());
                    $matiere_choisie = $faker->randomElement($classe_choisie->getMatieres());
                    $bord->setEditor($faker->randomElement($this->users))
                        ->addSection($section_choisie)
                        ->addFiliere($filiere_choisie)
                        ->addClasse($classe_choisie)
                        ->addMatiere($matiere_choisie)
                        ->setCollection($faker->randomElement($user->getCollectionBords()))
                        ->setTitle($faker->sentence($faker->numberBetween(2, 5)))
                        ->setAuthor($faker->name)
                        ->setKeyword($faker->sentence($faker->numberBetween(2, 15) ))
                        ->setAllUser($faker->numberBetween(0, 5455))
                        ->setNumbPage($faker->numberBetween(20, 300))
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setStar($faker->numberBetween(1, 5));
                    if($faker->boolean(70)){
                        $bord->setPrice($faker->numberBetween(0, 3000))
                            ->setSmallDescription($faker->paragraph($faker->numberBetween(2, 5)))
                            ->setFullDescription($faker->paragraph($faker->numberBetween(3, 20)))
                            ->setLastUpdateAt(new \DateTimeImmutable());;
                    }
                    $bord->setAllGainBord(100)
                        ->setAllGainInfooxschool(100)
                        ->setIsPublished($faker->boolean(70));
                    $manager->persist($bord);
                }
            }
        }



        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
            CollectionBordFixture::class,
            SectionFixture::class,
            ClasseFixture::class,
            FiliereFixture::class,
            MatiereFixture::class,
        ];
    }
}
