<?php

namespace App\DataFixtures\Test;

use App\DataFixtures\Salle;
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

        $salleC005 = new Salle();
        $salleC005->setNom('C005')
            ->setOrientation("No")
            ->setNombreFenetre(6)
            ->setNombrePorte(2)
            ->setContientpc(false)
            ->setBatiment("Bâtiment C")
            ->setSystemeAcquisition($sa18);
        $manager->persist($salleC005);

        $manager->flush();
    }
}
