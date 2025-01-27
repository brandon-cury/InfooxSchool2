<?php

namespace App\DataFixtures;

use App\Entity\CollectionBord;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CollectionBordFixture extends Fixture implements DependentFixtureInterface
{
    private array $users;
    public function load(ObjectManager $manager): void
    {
        $this->users = $manager->getRepository(User::class)->findAll();
        $faker = Factory::create('fr_FR');
        foreach ($this->users as $user) {
            if($faker->boolean(50)){
                for ($i = 1; $i <= $faker->numberBetween(1, 7); $i++) {
                    $collectionBord = new CollectionBord();
                    $collectionBord->setEditor($user);
                    $collectionBord->setTitle($faker->sentence($faker->numberBetween(1, 3)));
                    $manager->persist($collectionBord);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class];
    }
}
