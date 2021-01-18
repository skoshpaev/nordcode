<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserRepository $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * Gives default user admin@admin.admin/qwerty123 with tasks
     * @param ObjectManager $manager
     */
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

        $i = 0;
        while ($i < 100) {
            $task = new Task();

            $task->setTitle(' Title ' . $i);
            $task->setUser($this->userRepository->find($user->getId()));
            $task->setDate(DateTime::createFromFormat('j-M-Y', '15-Feb-2009'));
            $task->setTimespent($i);

            $manager->persist($task);
            $manager->flush();
            $i++;
        }
    }
}
