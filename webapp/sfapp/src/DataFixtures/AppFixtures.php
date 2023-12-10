<?php

namespace App\DataFixtures;

use App\Entity\Batiment;
use App\Entity\Releve;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $batiment = new Batiment();
        $batiment->setNom("Batiment D");
        $batiment->setDepartement("Info");
        $manager->persist($batiment);

        $batiment2 = new Batiment();
        $batiment2->setNom("Batiment C");
        $batiment2->setDepartement("R & T");
        $manager->persist($batiment2);

        $salle1 = new Salle();
        $salle1->setNom("302")
            ->setOrientation("No")
            ->setNbfenetres(6)
            ->setNbportes(2)
            ->setContientpc(true)
            ->setBatiment($batiment);

        $manager->persist($salle1);

        $salle2 = new Salle();
        $salle2->setNom("303")
            ->setOrientation("Su")
            ->setNbfenetres(2)
            ->setNbportes(1)
            ->setContientpc(false)
            ->setBatiment($batiment);

        $manager->persist($salle2);

        $salle3 = new Salle();

        $salle3->setNom("004")
            ->setOrientation("Es")
            ->setNbfenetres(6)
            ->setNbportes(1)
            ->setContientpc(true)
            ->setBatiment($batiment2);

        $manager->persist($salle3);

        $sa1 = new SystemeAcquisition();
        $sa1->setAdresseMac("00:00:00:00:00:01")
            ->setSalle($salle1);

        $salle1->setSystemeAcquisition($sa1);

        $manager->persist($sa1);

        $sa2 = new SystemeAcquisition();
        $sa2->setAdresseMac("00:00:00:00:00:02")
            ->setSalle($salle2);

        $manager->persist($sa2);

        $sa3 = new SystemeAcquisition();
        $sa3->setAdresseMac("00:00:00:00:00:03")
            ->setSalle($salle3);

        $manager->persist($sa3);

        $sa4 = new SystemeAcquisition();
        $sa4->setAdresseMac("00:00:00:00:00:04");

        $manager->persist($sa4);
        $releve1 = new Releve();
        $releve1->setHorodatage(new \DateTime());
        $releve1->setTemperature(20.0);
        $releve1->setHumidite(50.0);
        $releve1->setQualiteAir(400);
        $releve1->setSystemeAcquisition($sa1);
        $releve1->setSalle($sa1->getSalle());

        $manager->persist($releve1);

        $releve2 = new Releve();
        $releve2->setHorodatage(new \DateTime());
        $releve2->setTemperature(21.0);
        $releve2->setHumidite(51.0);
        $releve2->setQualiteAir(444);
        $releve2->setSystemeAcquisition($sa1);
        $releve2->setSalle($sa1->getSalle());

        $manager->persist($releve2);

        $manager->flush();

    }
}
