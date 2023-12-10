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
        // $product = new Product();
        // $manager->persist($product);

        $batiment = new Batiment();
        $batiment->setNom("Batiment D");
        $manager->persist($batiment);

        $batiment2 = new Batiment();
        $batiment2->setNom("Batiment C");
        $manager->persist($batiment2);

        $salle1 = new Salle();
        $salle1->setNumero("302")
            ->setOrientation("Nord")
            ->setNbfenetres(6)
            ->setNbportes(2)
            ->setContientpc(true)
            ->setBatiment($batiment);

        $manager->persist($salle1);

        $salle2 = new Salle();
        $salle2->setNumero("303")
            ->setOrientation("Sud")
            ->setNbfenetres(2)
            ->setNbportes(1)
            ->setContientpc(false)
            ->setBatiment($batiment);

        $manager->persist($salle2);

        $salle3 = new Salle();

        $salle3->setNumero("004")
            ->setOrientation("Est")
            ->setNbfenetres(6)
            ->setNbportes(1)
            ->setContientpc(true)
            ->setBatiment($batiment2);

        $manager->persist($salle3);

        $sa1 = new SystemeAcquisition();
        $sa1->setAdresseMac("00:00:00:00:00:01")
            ->setSalle($salle1);

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

        $utilisateur = new Utilisateur();
        $utilisateur->setIdentifiant("testChargeDeMission")
                    ->setMotDePasse("testChargeDeMission")
                    ->addRole("ROLE_CHARGE_DE_MISSION");

        $manager->persist($utilisateur);

        $manager->flush();
    }
}
