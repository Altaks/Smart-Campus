<?php

namespace App\DataFixtures;

use App\Entity\DemandeTravaux;
use App\Entity\Salle;
use App\Entity\Seuil;
use App\Entity\SystemeAcquisition;
use App\Entity\Utilisateur;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $listeGroupe = [ "l1", "l2", "m1", "m2"]; // rajouter les groupes ici ("k1", "k2")
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


        // Systèmes d'acquisition 4 à 18 ( 3 de marge pour tester les ajouts )
        $esp_004 = new SystemeAcquisition();
        $esp_004->setNom("ESP-004")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdk2eq1");

        $esp_005 = new SystemeAcquisition();
        $esp_005->setNom("ESP-005")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdk2eq2");

        $esp_006 = new SystemeAcquisition();
        $esp_006->setNom("ESP-006")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdk2eq3");

        $esp_007 = new SystemeAcquisition();
        $esp_007->setNom("ESP-007")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdl1eq1");

        $esp_008 = new SystemeAcquisition();
        $esp_008->setNom("ESP-008")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdl1eq2");

        $esp_009 = new SystemeAcquisition();
        $esp_009->setNom("ESP-009")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdl1eq3");

        $esp_010 = new SystemeAcquisition();
        $esp_010->setNom("ESP-010")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdl2eq1");

        $esp_011 = new SystemeAcquisition();
        $esp_011->setNom("ESP-011")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdl2eq2");

        $esp_012 = new SystemeAcquisition();
        $esp_012->setNom("ESP-012")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdl2eq3");

        $esp_013 = new SystemeAcquisition();
        $esp_013->setNom("ESP-013")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdm1eq1");

        $esp_014 = new SystemeAcquisition();
        $esp_014->setNom("ESP-014")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdm1eq2");

        $esp_015 = new SystemeAcquisition();
        $esp_015->setNom("ESP-015")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdm1eq3");

        $esp_016 = new SystemeAcquisition();
        $esp_016->setNom("ESP-016")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdm2eq1");

        $esp_017 = new SystemeAcquisition();
        $esp_017->setNom("ESP-017")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdm2eq2");

        $esp_018 = new SystemeAcquisition();
        $esp_018->setNom("ESP-018")
            ->setEtat("Non installé")
            ->setBaseDonnees("sae34bdm2eq3");


        // liste des salles ( 3 de marge pour tester les ajouts )

        $D204 = new Salle();
        $D204->setNom("D204")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(6)
            ->setContientPc(true)
            ->setOrientation("Su");


        $D203 = new Salle();
        $D203->setNom("D203")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(6)
            ->setContientPc(true)
            ->setOrientation("No");

        $D303 = new Salle();
        $D303->setNom("D303")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(6)
            ->setContientPc(true)
            ->setOrientation("No");


        $D304 = new Salle();
        $D304->setNom("D304")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(6)
            ->setContientPc(true)
            ->setOrientation("Su");

        $D101 = new Salle();
        $D101->setNom("D101")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(6)
            ->setContientPc(true)
            ->setOrientation("No");

        $D109 = new Salle();
        $D109->setNom("D109")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(6)
            ->setContientPc(true)
            ->setOrientation("No");


        $secreteriat = new Salle();
        $secreteriat->setNom("Secreteriat")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(2)
            ->setNombreFenetre(2)
            ->setContientPc(true)
            ->setOrientation("Su");

        $D001 = new Salle();
        $D001->setNom("D001")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("No");

        $D002 = new Salle();
        $D002->setNom("D002")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("Su");

        $D004 = new Salle();
        $D004->setNom("D004")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("Su");

        $C004 = new Salle();
        $C004->setNom("C004")
            ->setBatiment("Bâtiment C")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("Su");

        $C007 = new Salle();
        $C007->setNom("C007")
            ->setBatiment("Bâtiment C")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("No");

        $D201 = new Salle();
        $D201->setNom("D201")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("No");

        $D307 = new Salle();
        $D307->setNom("D307")
            ->setBatiment("Bâtiment D")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("No");

        $C005 = new Salle();
        $C005->setNom("C005")
            ->setBatiment("Bâtiment C")
            ->setNombrePorte(1)
            ->setNombreFenetre(4)
            ->setContientPc(true)
            ->setOrientation("No");


        // 3 demandes d'installation
        $maintenant = new DateTime();
        $cinq_jours_avant = new DateTime();
        $cinq_jours_avant->setTimestamp(time() - 5*24*60*60);
        $dix_jours_avant = new DateTime();
        $dix_jours_avant->setTimestamp(time() - 10*24*60*60);
        $vingt_jours_avant = new DateTime();
        $vingt_jours_avant->setTimestamp(time() - 20*24*60*60);
        $vingt_cinq_jours_avant = new DateTime();
        $vingt_cinq_jours_avant->setTimestamp(time() - 25*24*60*60);
        $trente_jours_avant = new DateTime();
        $trente_jours_avant->setTimestamp(time() - 30*24*60*60);
        $sept_jours_cinq_heures_trente_minutes_avant = new DateTime();
        $sept_jours_cinq_heures_trente_minutes_avant->setTimestamp(time() - (7*24*60*60 + 5*60*60 + 30*60));
        $quatres_jours_dis_heures_dix_minutes_avant = new DateTime();
        $quatres_jours_dis_heures_dix_minutes_avant->setTimestamp(time() - (14*24*60*60 + 10*60*60 + 10*60));

        $demandeInstallation1 = new DemandeTravaux();
        $demandeInstallation1->setSalle($D204)
            ->setType("Installation")
            ->setTerminee(false)
            ->setDate($maintenant); // now

        $demandeInstallation2 = new DemandeTravaux();
        $demandeInstallation2->setSalle($D203)
            ->setType("Installation")
            ->setTerminee(false)
            ->setDate($cinq_jours_avant); // 5 jours avant

        $demandeInstallation3 = new DemandeTravaux();
        $demandeInstallation3->setSalle($D303)
            ->setType("Installation")
            ->setTerminee(false)
            ->setDate($dix_jours_avant); // 10 jours avant

        // 3 demandes de réparations

        // installations des sa dans les salles

        $D304->setSystemeAcquisition($esp_007);
        $D101->setSystemeAcquisition($esp_008);
        $D109->setSystemeAcquisition($esp_009);

        // demandes d'intalations terminée pour les 3 salles
        $demandeIntalation4 = new DemandeTravaux();
        $demandeIntalation4->setSalle($D304)
            ->setType("Installation")
            ->setTerminee(true)
            ->setSystemeAcquisition($esp_007)
            ->setDate($trente_jours_avant); // 30 jours avant

        $demandeIntalation5 = new DemandeTravaux();
        $demandeIntalation5->setSalle($D101)
            ->setType("Installation")
            ->setTerminee(true)
            ->setSystemeAcquisition($esp_008)
            ->setDate($vingt_jours_avant); // 20 jours avant

        $demandeIntalation6 = new DemandeTravaux();
        $demandeIntalation6->setSalle($D109)
            ->setType("Installation")
            ->setTerminee(true)
            ->setSystemeAcquisition($esp_009)
            ->setDate($vingt_cinq_jours_avant); // 25 jours avant

        // installations des dt dans les salles
        $demandeRep1 = new DemandeTravaux();
        $demandeRep1->setSalle($D304)
            ->setType("Réparation")
            ->setTerminee(false)
            ->setSystemeAcquisition($esp_007)
            ->setEmailDemandeur("ksimon@etudiant.univ-lr.fr")
            ->setCommentaire("Le système d'acquisition ne fonctionne pas")
            ->setDate($maintenant); // maintenant
        $esp_007->setEtat("Réparation");

        $demandeRep2 = new DemandeTravaux();
        $demandeRep2->setSalle($D101)
            ->setType("Réparation")
            ->setTerminee(false)
            ->setSystemeAcquisition($esp_008)
            ->setEmailDemandeur("ksimon@etudiant.univ-lr.fr")
            ->setCommentaire("Le système d'acquisition ne fonctionne pas")
            ->setDate($sept_jours_cinq_heures_trente_minutes_avant); // 7 jours, 5h et 30 minutes avant

        $esp_008->setEtat("Réparation");

        $demandeRep3 = new DemandeTravaux();
        $demandeRep3->setSalle($D109)
            ->setType("Réparation")
            ->setTerminee(false)
            ->setSystemeAcquisition($esp_009)
            ->setEmailDemandeur("ksimon@etudiant.univ-lr.fr")
            ->setCommentaire("Le système d'acquisition ne fonctionne pas")
            ->setDate($quatres_jours_dis_heures_dix_minutes_avant); // 14 jours, 10h et 10 minutes avant
        $esp_009->setEtat("Réparation");


        // 6 esp opérationnels (18 obligatoire)

        $esp_010->setEtat("Opérationnel");
        $esp_011->setEtat("Opérationnel");
        $esp_012->setEtat("Opérationnel");
        $esp_013->setEtat("Opérationnel");
        $esp_014->setEtat("Opérationnel");
        $esp_018->setEtat("Opérationnel");

        // mise en place dans les salles

        $secreteriat->setSystemeAcquisition($esp_010);
        $D001->setSystemeAcquisition($esp_011);
        $D002->setSystemeAcquisition($esp_012);
        $D004->setSystemeAcquisition($esp_013);
        $C004->setSystemeAcquisition($esp_014);
        $C005->setSystemeAcquisition($esp_018);
        
        $manager->persist($esp_004);
        $manager->persist($esp_005);
        $manager->persist($esp_006);
        $manager->persist($esp_007);
        $manager->persist($esp_008);
        $manager->persist($esp_009);
        $manager->persist($esp_010);
        $manager->persist($esp_011);
        $manager->persist($esp_012);
        $manager->persist($esp_013);
        $manager->persist($esp_014);
        $manager->persist($esp_015);
        $manager->persist($esp_016);
        $manager->persist($esp_017);
        $manager->persist($esp_018);
        
        $manager->persist($D204);
        $manager->persist($D203);
        $manager->persist($D303);
        $manager->persist($D304);
        $manager->persist($D101);
        $manager->persist($D109);
        $manager->persist($secreteriat);
        $manager->persist($D001);
        $manager->persist($D002);
        $manager->persist($D004);
        $manager->persist($C004);
        $manager->persist($C007);
        $manager->persist($D201);
        $manager->persist($D307);
        $manager->persist($C005);
        
        $manager->persist($demandeInstallation1);
        $manager->persist($demandeInstallation2);
        $manager->persist($demandeInstallation3);
        $manager->persist($demandeIntalation4);
        $manager->persist($demandeIntalation5);
        $manager->persist($demandeIntalation6);
        $manager->persist($demandeRep1);
        $manager->persist($demandeRep2);
        $manager->persist($demandeRep3);
        


        $seuil_temp_min = new Seuil();
        $seuil_temp_min->setNom("temp_min")
            ->setValeur("19");

        $manager->persist($seuil_temp_min);

        $seuil_temp_max = new Seuil();
        $seuil_temp_max->setNom("temp_max")
            ->setValeur("28");

        $manager->persist($seuil_temp_max);

        $seuil_humidite_max = new Seuil();
        $seuil_humidite_max->setNom("humidite_max")
            ->setValeur("70");

        $manager->persist($seuil_humidite_max);

        $seuil_co2_premier_palier = new Seuil();
        $seuil_co2_premier_palier->setNom("co2_premier_palier")
            ->setValeur("1000");

        $manager->persist($seuil_co2_premier_palier);

        $seuil_co2_second_palier = new Seuil();
        $seuil_co2_second_palier->setNom("co2_second_palier")
            ->setValeur("1500");

        $manager->persist($seuil_co2_second_palier);

        $manager->flush();
    }

}
