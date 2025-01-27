<?php

namespace App\DataFixtures;

use App\Entity\Bord;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    private array $bords;
    private array $users;

    public function load(ObjectManager $manager): void
    {
        $this->bords = $manager->getRepository(Bord::class)->findAll();
        $this->users = $manager->getRepository(User::class)->findAll();
        $faker= Factory::create();
        foreach ($this->bords as $bord){
            for($i=1; $i<= $faker->numberBetween(0, 20); $i++){
                $comment = new Comment();
                $comment->setContent($faker->paragraph)
                    ->setPublished($faker->boolean(80))
                    ->setSend($faker->boolean(80))
                    ->setRating($faker->numberBetween(1, 5))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setBord($bord)
                    ->setUser($faker->randomElement($this->users));
                $manager->persist($comment);
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
