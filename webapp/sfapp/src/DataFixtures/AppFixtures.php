<?php

namespace App\DataFixtures;

use App\Entity\Batiment;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

    }

}
