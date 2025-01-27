<?php

namespace App\DataFixtures;

use App\Entity\Filiere;
use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FiliereFixture extends Fixture implements DependentFixtureInterface
{
    private array $sections;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->sections = $manager->getRepository(Section::class)->findAll();
        $a = 1;
        foreach ($this->sections as $section) {
            $filiere1 = new Filiere();
            $filiere1->setTitle('filiere' . $a)
                ->addSection($section)
                ->setSort($a)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 Days', 'now')))
                ->setAllUser($faker->numberBetween(0,1000))
                ->setImage($faker->imageUrl($width = 640, $height = 480));
            $manager->persist($filiere1);
            $a++;
        }
        for ($i = 2; $i <= 30; $i++) {
            $filiere = new Filiere();
            $filiere->setTitle($faker->word)
                    ->addSection($faker->randomElement($this->sections))
                    ->setSort($i)
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 Days', 'now')))
                    ->setAllUser($faker->numberBetween(0,1000))
                    ->setImage($faker->imageUrl($width = 640, $height = 480));
            $manager->persist($filiere);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SectionFixture::class
        ];
    }
}
