<?php

namespace App\DataFixtures;

use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SectionFixture extends Fixture
{
    private array $sections;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $sections = [
            'Primaire',
            'Secondaire Général',
            'Secondaire Technique',
            'Section anglophone',
            'Supérieur',
            'Prépa-concours',
            'Astuces scolaires'
        ];
        foreach ($sections as $title) {
            $section = new Section();
            $section->setTitle($title)
            ->setSort($faker->numberBetween(1,10));
            $manager->persist($section);

        }
        $manager->flush();
    }
}
