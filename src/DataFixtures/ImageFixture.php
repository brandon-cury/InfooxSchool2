<?php

namespace App\DataFixtures;

use App\Entity\Bord;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ImageFixture extends Fixture implements DependentFixtureInterface
{
    private array $bords = [];
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->bords = $manager->getRepository(Bord::class)->findAll();
        foreach ($this->bords as $bord) {

            if($faker->boolean(70)){
                $image1 = new Image();
                $image1->setBord($bord)
                        ->setTitle($faker->paragraph)
                        ->setPath($faker->imageUrl($width = 640, $height = 480))
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setSort(1);
                $manager->persist($image1);
                if($faker->boolean(50)){
                    for ($i = 1; $i <= $faker->numberBetween(1, 10); $i++) {
                        $image2 = new Image();
                        $image2->setBord($bord)
                            ->setTitle($faker->paragraph)
                            ->setPath($faker->imageUrl($width = 640, $height = 480))
                            ->setCreatedAt(new \DateTimeImmutable())
                            ->setSort($i);
                        $manager->persist($image2);
                    }
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            BordFixture::class,
        ];
    }
}
