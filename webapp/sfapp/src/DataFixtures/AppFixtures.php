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
        $salle1->setNom("D302")
            ->setOrientation("No")
            ->setNbfenetres(6)
            ->setNbportes(2)
            ->setContientpc(true)
            ->setBatiment($batiment);

        $manager->persist($salle1);

        $salle2 = new Salle();
        $salle2->setNom("D303")
            ->setOrientation("Su")
            ->setNbfenetres(2)
            ->setNbportes(1)
            ->setContientpc(false)
            ->setBatiment($batiment);

        $manager->persist($salle2);

        $salle3 = new Salle();

        $salle3->setNom("C004")
            ->setOrientation("Es")
            ->setNbfenetres(6)
            ->setNbportes(1)
            ->setContientpc(true)
            ->setBatiment($batiment2);

        $manager->persist($salle3);

        $salle4 = new Salle();
        $salle4->setNom("D304")
            ->setOrientation("Su")
            ->setNbfenetres(2)
            ->setNbportes(1)
            ->setContientpc(true)
            ->setBatiment($batiment);

        $manager->persist($salle4);

        $sa1 = new SystemeAcquisition();
        $sa1->setAdresseMac("00:00:00:00:00:01")
            ->setSalle($salle1)
            ->setTag(1);

        $manager->persist($sa1);

        $sa2 = new SystemeAcquisition();
        $sa2->setAdresseMac("00:00:00:00:00:02")
            ->setSalle($salle2)
            ->setTag(2);

        $manager->persist($sa2);

        $sa3 = new SystemeAcquisition();
        $sa3->setAdresseMac("00:00:00:00:00:03")
            ->setSalle($salle3)
            ->setTag(3);

        $manager->persist($sa3);

        $sa4 = new SystemeAcquisition();
        $sa4->setAdresseMac("00:00:00:00:00:04");
        $sa4->setTag(4);

        $manager->persist($sa4);

        $utilisateur = new Utilisateur();
        $utilisateur->setIdentifiant("testChargeDeMission")
                    ->setMotDePasse("testChargeDeMission")
                    ->addRole("ROLE_CHARGE_DE_MISSION");

        $manager->persist($utilisateur);

        $utilisateur2 = new Utilisateur();
        $utilisateur2->setIdentifiant("testTechnicien")
            ->setMotDePasse("testTechnicien")
            ->addRole("ROLE_TECHNICIEN");

        $manager->persist($utilisateur2);

        $manager->flush();
    }
}
