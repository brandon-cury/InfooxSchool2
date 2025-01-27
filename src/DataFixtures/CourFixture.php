<?php

namespace App\DataFixtures;

use App\Entity\Bord;
use App\Entity\Cour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourFixture extends Fixture implements DependentFixtureInterface
{
    private array $bords;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->bords = $manager->getRepository(Bord::class)->findAll();
        foreach ($this->bords as $bord) {
            for ($i = 1; $i <= $faker->numberBetween(4, 10); $i++) {
                $cour = new Cour();
                $cour->setTitle($faker->paragraph)
                    ->setbord($bord)
                    ->setContent('bords/test/cours/test.pdf');
                if($faker->boolean(50)){
                    $cour->setVideoLink('vmytMK1ZjcY')
                        ->setVideoImg('bords/test/images/video.jpg')
                        ->setYoutube(true);
                }else{
                    $cour->setYoutube(false);
                }
                $cour->setSort($i)
                    ->setContainer($faker->boolean(70));
                $manager->persist($cour);
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
