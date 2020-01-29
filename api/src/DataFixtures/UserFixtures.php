<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $enabled = $publicVisibility = $publicEmail = true;
        $user = new User(
            'Discoveryfy', 'discoverify.admin@fabri.cat',
            $enabled, $publicVisibility, $publicEmail,
            [ USER::ADMIN ]
        );
        $user->setPassword($this->passwordEncoder->encodePassword($user,'1234567890'));

        $manager->persist($user);
        $manager->flush();
    }
}
