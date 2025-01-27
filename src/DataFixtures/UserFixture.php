<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private object $hasher;

    private array $genders = ['male', 'female'];
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for($i = 1; $i <= 20; $i++){
            $user = new User();
            $gender = $faker->randomElement($this->genders); //cherche une valeur aleatoire dans un tableau
            $gender = ($gender == 'male') ? 'm' : 'f';
            $user->setEmail($faker->email)
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName);
            if($faker->boolean(70)){
                $user->setTel(670501518)
                    ->setCodeTel(237)
                    ->setAvatar('0'.($i + 10). $gender. '.jpg');
            }
            $user->setNumberAffiliated(5)
                ->setcreatedAt(new \DateTimeImmutable());
            if($faker->boolean(45)){

                $user->setupdatedAt(new \DateTimeImmutable())
                ->setEmailVerifiedAt(new \DateTimeImmutable());
            }
            $manager->persist($user);
        }
        $manager->flush();

        // Admin John Doe
        $user = new User();
        $user->setEmail('john.doe@gmail.com')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setTel(670104245)
            ->setCodeTel(237)
            ->setAvatar('074m.jpg')
            ->setNumberAffiliated(5)
            ->setcreatedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $manager->flush();



        // Admin Pat Mar
        $user = new User();
        $user->setEmail('pat.mar@gmail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setFirstName('pat')
            ->setLastName('Mar')
            ->setTel(670501518)
            ->setCodeTel(237)
            ->setAvatar('012m.jpg')
            ->setNumberAffiliated(5)
            ->setcreatedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $manager->flush();

        // user alex dol
        $user = new User();
        $user->setEmail('alex.dol@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setFirstName('alex')
            ->setLastName('dol')
            ->setTel(670104245)
            ->setCodeTel(237)
            ->setAvatar('012m.jpg')
            ->setNumberAffiliated(5)
            ->setcreatedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $manager->flush();
    }
}
