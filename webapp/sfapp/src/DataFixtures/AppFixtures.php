<?php

namespace App\DataFixtures;

use App\Entity\DemandeTravaux;
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
        $utilisateur = new Utilisateur();
        $utilisateur->setIdentifiant("yghamri")
            ->setMotDePasse("pwd-yghamri")
            ->addRole("ROLE_CHARGE_DE_MISSION");

        $manager->persist($utilisateur);

        $utilisateur2 = new Utilisateur();
        $utilisateur2->setIdentifiant("jmalki")
            ->setMotDePasse("pwd-jmalki")
            ->addRole("ROLE_TECHNICIEN");

        $manager->persist($utilisateur2);


        $sa1 = new SystemeAcquisition();
        $sa1->setNom('ESP-003')
            ->setBaseDonnees("sae34bdm2eq3")
            ->setEtat("Opérationnel");

        $salle1 = new Salle();
        $salle1->setNom("D302")
            ->setOrientation("Su")
            ->setNombreFenetre(6)
            ->setNombrePorte(2)
            ->setContientpc(true)
            ->setBatiment("Bâtiment D")
            ->setSystemeAcquisition($sa1);

        $sa1->setSalle($salle1);

        $travaux1 = new DemandeTravaux();
        $travaux1->setDate(new \DateTime('2023-12-17 09:00:00'))
            ->setType('Installation')
            ->setSystemeAcquisition($sa1)
            ->setSalle($salle1)
            ->setTerminee(true);

        $manager->persist($sa1);
        $manager->persist($salle1);
        $manager->persist($travaux1);

        $sa2 = new SystemeAcquisition();
        $sa2->setNom('ESP-007')
            ->setBaseDonnees("sae34bdm1eq2")
            ->setEtat("Installation");

        $salle2 = new Salle();
        $salle2->setNom("C004")
            ->setOrientation("Su")
            ->setNombreFenetre(6)
            ->setNombrePorte(2)
            ->setContientpc(true)
            ->setBatiment("Bâtiment C");

        $travaux2 = new DemandeTravaux();
        $travaux2->setDate(new \DateTime('2023-12-19 13:00:00'))
            ->setType('Installation')
            ->setSystemeAcquisition($sa2)
            ->setSalle($salle2)
            ->setTerminee(false);

        $manager->persist($sa2);
        $manager->persist($salle2);
        $manager->persist($travaux2);



        $sa3 = new SystemeAcquisition();
        $sa3->setNom('ESP-014')
            ->setBaseDonnees("sae34bdl2eq1")
            ->setEtat("Non installé");

        $salle3 = new Salle();
        $salle3->setNom("D201")
            ->setOrientation("No")
            ->setNombreFenetre(2)
            ->setNombrePorte(1)
            ->setContientpc(false)
            ->setBatiment("Bâtiment D");


        $manager->persist($sa3);
        $manager->persist($salle3);


        $sa4 = new SystemeAcquisition();
        $sa4->setNom('ESP-018')
            ->setBaseDonnees("sae34bdm2eq3")
            ->setEtat("Non installé");

        $salle4 = new Salle();
        $salle4->setNom("D204")
            ->setOrientation("Su")
            ->setNombreFenetre(6)
            ->setNombrePorte(2)
            ->setContientpc(true)
            ->setBatiment("Bâtiment D");

        $travaux4 = new DemandeTravaux();
        $travaux4->setDate(new \DateTime('2023-12-20 17:00:00'))
            ->setType('Installation')
            ->setSalle($salle4)
            ->setSystemeAcquisition($sa4)
            ->setTerminee(false);

        $manager->persist($sa4);
        $manager->persist($salle4);
        $manager->persist($travaux4);


        $manager->flush();
    }

}
