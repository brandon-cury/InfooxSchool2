<?php

namespace App\DataFixtures;

use App\Entity\Cour;
use App\Entity\Exercice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExerciceFixture extends Fixture implements DependentFixtureInterface
{
    private array $cours;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->cours = $manager->getRepository(Cour::class)->findAll();
        foreach($this->cours as $cour){
            if($faker->boolean(70)){
                for ($i = 1; $i <= $faker->numberBetween(4, 10); $i++) {
                    $exercice = new Exercice();
                    $exercice->setEditor($cour->getBord()->getEditor())
                            ->setCour($cour)
                            ->setTitle($faker->paragraph)
                            ->setContent('bords/test/pdf/test.pdf')
                            ->setCorrected('bords/test/pdf/test.pdf');
                            if($faker->boolean(60)){
                                $exercice->setVideoLink('vmytMK1ZjcY')
                                    ->setVideoImg('bords/test/images/video.jpg')
                                    ->setYoutube(true);
                            }else{
                                $exercice->setYoutube(false);
                            }
                            $exercice->setSort($i)
                                ->setContainer($faker->boolean(70));
                            $manager->persist($exercice);
                }
            }

        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CourFixture::class,
        ];
    }
}
