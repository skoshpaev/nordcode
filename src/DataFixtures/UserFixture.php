<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setPassword($this->passwordEncoder->encodePassword(
             $user,
             'qwerty123'
         ));
        $user->setEmail('admin@admin.admin');
        $user->setRoles(['ROLE_AUTHENTICATED_USER']);

        $manager->persist($user);
        $manager->flush();
    }
}
