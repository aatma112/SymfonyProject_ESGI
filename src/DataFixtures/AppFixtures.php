<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ){

    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        // User Admin
        $user = (new User())
        ->setEmail(email:'admin@admin.com')
        ->setFirstName(firstName: 'Admin')
        ->setLastNAme(lastName: 'User')
        ->setRoles(roles: ["ROLE_ADMIN"])
        ->setPassword(
            password: $this->hasher->hashPassword(new User, 'itsatest')
        );
        $manager->persist($user);

        // Generation of 10 reandom users
        for ($i = 1; $i <= 10; $i++){
            $user = (new User())
            ->setEmail(email: "user_$i@admin.com") //'user_.$i.@admin.com'
            ->setFirstName("User $i")
            ->setLastName("Test")
            ->setPassword
            ($this->hasher->hashPassword(new User, plainPassword: 'itsatest'))
            ;
        

        $manager->persist($user);
        }

        $manager->flush();
    }
}
