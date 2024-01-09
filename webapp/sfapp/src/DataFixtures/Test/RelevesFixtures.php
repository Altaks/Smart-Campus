<?php

namespace App\DataFixtures\Test;

use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RelevesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sa18 = new SystemeAcquisition();
        $sa18->setNom('ESP-018')
            ->setBaseDonnees("sae34bdm2eq3")
            ->setEtat("Opérationnel");
        $manager->persist($sa18);

        $salleC004 = new Salle();
        $salleC004->setNom('C004')
            ->setOrientation("No")
            ->setNombreFenetre(6)
            ->setNombrePorte(2)
            ->setContientpc(false)
            ->setBatiment("Bâtiment C")
            ->setSystemeAcquisition($sa18);
        $manager->persist($salleC004);

        $manager->flush();
    }
}
