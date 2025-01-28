<?php

namespace App\DataFixtures;

use App\Entity\Bord;
use App\Entity\Epreuve;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpreuveFixture extends Fixture implements DependentFixtureInterface
{
    private array $bords;

    public function load(ObjectManager $manager): void
    {
        $faker  = Factory::create('fr_FR');
        $this->bords = $manager->getRepository(Bord::class)->findAll();
        foreach ($this->bords as $bord) {
            for ($i = 1; $i <= $faker->numberBetween(4, 10); $i++) {
                $epreuve = new Epreuve();
                $epreuve->setTitle($faker->paragraph)
                    ->setBord($bord)
                    ->setEditor($bord->getEditor())
                    ->setAllUser($faker->numberBetween(1, 10000))
                    ->setContent('bords/test/pdf/test.pdf')
                    ->setCorrected('bords/test/pdf/test.pdf');
                if($faker->boolean(60)){
                    $epreuve->setVideoLink('xVhLwrNuVkI')
                        ->setVideoImg('bords/test/images/video.jpg');
                }
                if($faker->boolean(20)){
                    $epreuve->setImage($faker->imageUrl($width = 640, $height = 480));
                }
                $epreuve->setSort($i)
                ->setPath('bords/test')
                ->setStar($faker->numberBetween(0,5))
                ->setContainer($faker->boolean(70))
                ->setOnligne($faker->boolean(80))
                ->setUpdateAt(new \DateTimeImmutable());
                $manager->persist($epreuve);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
          BordFixture::class,
            UserFixture::class
        ];
    }
}
