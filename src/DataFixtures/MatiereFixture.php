<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use App\Entity\Matiere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MatiereFixture extends Fixture implements DependentFixtureInterface
{
    private array $classes;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->classes = $manager->getRepository(Classe::class)->findAll();
        $i=1;
        foreach ($this->classes as $classe) {
            $matiere = new Matiere();
            $matiere->setTitle('Matière ' . $i)
                    ->addClasse($classe)
                    ->setAllUser($faker->numberBetween(1, 1000))
                    ->setSort(1);
            $manager->persist($matiere);
            $i++;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ClasseFixture::class
        ];
    }
}
