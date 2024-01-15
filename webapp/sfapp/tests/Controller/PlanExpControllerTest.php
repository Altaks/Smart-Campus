<?php

namespace App\tests\Controller;

use App\Entity\SystemeAcquisition;
use App\Entity\Salle;
use App\Service\SeuilService;
use App\Repository\DemandeTravauxRepository;
use App\Repository\SalleRepository;
use App\Repository\SystemeAcquisitionRepository;
use App\Repository\SeuilRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlanExpControllerTest extends WebTestCase
{
    public function test_cdm_demander_installation_sur_salle_invalide(): void
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        $client->loginUser($utilisateur);

        // La salle d'id -1 n'existe pas, le serveur doit renvoyer une erreur 404
        $client->request('GET', '/plan/-1/demander-installation');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_cdm_demander_installation_sur_salle_avec_sa(): void
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        $client->loginUser($utilisateur);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $systemeAcquisition = $client->getContainer()->get('doctrine')->getRepository('App\Entity\SystemeAcquisition')->findOneBy(['nom' => 'ESP-123']);

        if ($systemeAcquisition == null) {
            $systemeAcquisition = new SystemeAcquisition();
            $systemeAcquisition->setNom("ESP-123");
            $systemeAcquisition->setBaseDonnees("sae34bdk1eq1");
            $systemeAcquisition->setEtat("Opérationnel");

            $entityManager->persist($systemeAcquisition);
            $entityManager->flush();
        }

        $salle = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Salle')->findOneBy(['nom' => 'X001']);

        if ($salle == null) {
            $salle = new Salle();
            $salle->setNom("X001");
            $salle->setBatiment("Bâtiment X");
            $salle->setOrientation("No");
            $salle->setNombrePorte(1);
            $salle->setNombreFenetre(6);
            $salle->setSystemeAcquisition($systemeAcquisition);
            $salle->setContientPc(false);
            $entityManager->persist($salle);
            $entityManager->flush();
        }

        $sa = $entityManager->getRepository(SystemeAcquisition::class)->findOneBy(['nom' => 'ESP-123']);
        $this->assertNotNull($sa);
        $salle = $entityManager->getRepository(Salle::class)->findOneBy(['nom' => 'X001']);
        $this->assertNotNull($salle);

        // La salle d'id -1 n'existe pas, le serveur doit renvoyer une erreur 404
        $client->request('GET', '/plan/' . $salle->getId() . '/demander-installation');
        $this->assertResponseStatusCodeSame(404);

        $entityManager->remove($salle);
        $entityManager->remove($sa);
        $entityManager->flush();
    }

    public function test_cdm_plan_route_connexion_valide(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan');
        $this->assertResponseIsSuccessful();
    }

    public function test_cdm_plan_route_connexion_invalide_technicien(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_cdm_plan_route_connexion_invalide_usager(): void
    {
        $client = static::createClient();

        $client->request('GET', '/plan');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_demande_travaux_route_connexion_valide(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $travauxRepository = static::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findBy([
            'type' => 'Installation',
            'terminee' => false
        ]);

        for ($i = 0; $i < count($travaux); $i++) {
            $client->request('GET', '/plan/demande-travaux/' . $travaux[$i]->getId());
            $this->assertResponseIsSuccessful();
        }
    }

    public function test_demande_travaux_route_connexion_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $travauxRepository = static::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findAll();

        for ($i = 0; $i < count($travaux); $i++) {
            $client->request('GET', '/plan/demande-travaux/' . $travaux[$i]->getId());
            $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
        }
    }

    public function test_demande_travaux_route_connexion_invalide_usager(): void
    {
        $client = static::createClient();

        $travauxRepository = static::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findAll();

        for ($i = 0; $i < count($travaux); $i++) {
            $client->request('GET', '/plan/demande-travaux/' . $travaux[$i]->getId());
            $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        }
    }

    public function test_demande_travaux_nombre_sa_liste_correspond_nombre_sa_bd(){

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $systemesAcquisitionRepository = static::getContainer()->get(SystemeAcquisitionRepository::class);
        $nbSystemesAcquisitionNonInstalle = count($systemesAcquisitionRepository->findBy(['etat' => 'Non installé']));

        $travauxRepository = static::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findBy([
            'terminee' => false
        ]);

        for ($i = 0; $i < count($travaux); $i++) {
            $crawler = $client->request('GET', '/plan/demande-travaux/' . $travaux[$i]->getId());

            $select = $crawler->filter('#selectSa');
            $options = $select->children();
            $nbOptions = count($options);

            if ($travaux[$i]->getSystemeAcquisition() == null) {
                $this->assertEquals($nbSystemesAcquisitionNonInstalle + 1, $nbOptions);
            }
            else {
                $this->assertEquals($nbSystemesAcquisitionNonInstalle + 2, $nbOptions);
            }
        }

        $travaux = $travauxRepository->findBy([
            "type" => "Installation",
            "terminee" => false
        ]);

        for($i=0; $i<count($travaux); $i++) {
            $crawler = $client->request('GET', '/plan/demande-travaux/' . $travaux[$i]->getId());
            $button = $crawler->filter('button');
            if ($button->count()) {
                $this->assertEquals('Déclarer opérationnel', $button->eq(0)->text());
            }
            else {
                $p = $crawler->filter('p');
                $this->assertEquals('Pas de relevé',$p->eq(0)->text());
            }
        }
    }

    public function test_lister_SA_route_connexion_invalide_usager(): void
    {
        $client = static::createClient();
        $client->request('GET', '/plan/lister-sa');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_lister_SA_route_connexion_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister-sa');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_ajouter_sa_technicien_connexion_valide_technicien()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/ajouter-sa');
        $this->assertResponseIsSuccessful();
    }

    public function test_ajouter_sa_technicien_connexion_invalide_charge_de_mission()
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/ajouter-sa');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());

    }

    public function test_ajouter_sa_technicien_connexion_invalide_usager()
    {
        $client = static::createClient();
        $client->request('GET', '/plan/ajouter-sa');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_ajouter_sa_technicien_verifiacation_formulaire()
    {
        $client = static::createClient();
        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'jmalki']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/ajouter-sa');
        $this->assertResponseIsSuccessful();

        $crawler = $client->submitForm('submit', [
            'form[nom]' => 'ESP-999',
            'form[baseDonnees]' => 'sae34bdk1eq2'
        ]);

        $saRepository = static::getContainer()->get(SystemeAcquisitionRepository::class);
        $sa = $saRepository->findOneBy(['nom' => 'ESP-999']);
        $this->assertNotEmpty($sa);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->remove($sa);
        $entityManager->flush();
    }

    public function test_ajouter_salle_cdm_contenu_form():void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/ajouter-salle');

        $client->submitForm('submit', [
            'form[nom]' => 'E789',
            'form[batiment]' => 'Bâtiment P',
            'form[orientation]' => 'No',
            'form[nombreFenetre]' => 2,
            'form[nombrePorte]' => 2,
            'form[contientPc]' => 1
        ]);
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salle = $salleRepository->findOneBy(['nom' => 'E789']);
        $this->assertNotEmpty($salle);
        if(!empty($salle)) {
            $entityManager->remove($salle);
            $entityManager->flush();
        }

        $nbSalleInit = count($salleRepository->findAll());

        $client->request('GET', '/plan/ajouter-salle');

        $client->submitForm('submit', [
            'form[nom]' => 'D303',
            'form[batiment]' => 'Bâtiment D',
            'form[orientation]' => 'Su',
            'form[nombreFenetre]' => 6,
            'form[nombrePorte]' => 2,
            'form[contientPc]' => 1
        ]);

        $this->assertCount($nbSalleInit, $salleRepository->findAll());

        $client->request('GET', '/plan/ajouter-salle');

        $client->submitForm('submit', [
            'form[nom]' => '',
            'form[batiment]' => 'Bâtiment U',
            'form[orientation]' => 'NO',
            'form[nombreFenetre]' => 6,
            'form[nombrePorte]' => 2,
            'form[contientPc]' => 1
        ]);

        $this->assertCount($nbSalleInit, $salleRepository->findAll());

        $client->request('GET', '/plan/ajouter-salle');

        $client->submitForm('submit', [
            'form[nom]' => 'M542',
            'form[batiment]' => '',
            'form[orientation]' => 'NO',
            'form[nombreFenetre]' => 6,
            'form[nombrePorte]' => 2,
            'form[contientPc]' => 1
        ]);

        $this->assertCount($nbSalleInit, $salleRepository->findAll());
    }

    public function test_seuils_modification_connexion_valide_charge_de_mission()
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/seuils-alertes');
        $this->assertResponseIsSuccessful();
    }
    public function test_seuils_modification_connexion_invalide_technicien()
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'jmalki']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/seuils-alertes');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }
    public function test_seuils_modification_connexion_invalide_usager()
    {
        $client = static::createClient();
        $client->request('GET', '/plan/seuils-alertes');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_seuils_modification_verifiacation_formulaire()
    {
        $client = static::createClient();
        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/seuils-alertes');
        $this->assertResponseIsSuccessful();

        $seuilService = new SeuilService();

        $seuils = $seuilService->getSeuils($client->getContainer()->get('doctrine'));

        $temp_min_valeur_actuelle = $seuils['temp_min'];
        $temp_max_valeur_actuelle = $seuils['temp_max'];
        $humidite_max_valeur_actuelle = $seuils['humidite_max'];
        $co2_premier_palier_valeur_actuelle = $seuils['co2_premier_palier'];
        $co2_second_palier_valeur_actuelle = $seuils['co2_second_palier'];

        $rand_number = random_int(2,3);

        $crawler = $client->submitForm('submit', [
            'form[temp_min]' => $temp_min_valeur_actuelle + $rand_number,
            'form[temp_max]' => $temp_max_valeur_actuelle + $rand_number,
            'form[humidite_max]' => $humidite_max_valeur_actuelle + $rand_number,
            'form[co2_premier_palier]' => $co2_premier_palier_valeur_actuelle + $rand_number,
            'form[co2_second_palier]' => $co2_second_palier_valeur_actuelle + $rand_number,
        ]);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $seuilRepository = static::getContainer()->get(SeuilRepository::class);

        $seuils_temp_min = $seuilRepository->findOneBy(['nom' => 'temp_min']);
        $this->assertEquals($temp_min_valeur_actuelle + $rand_number,$seuils_temp_min->getValeur());
        $seuils_temp_min->setValeur($temp_min_valeur_actuelle);

        $seuils_temp_max = $seuilRepository->findOneBy(['nom' => 'temp_max']);
        $this->assertEquals($temp_max_valeur_actuelle + $rand_number,$seuils_temp_max->getValeur());
        $seuils_temp_max->setValeur($temp_max_valeur_actuelle);

        $seuils_humidite_max = $seuilRepository->findOneBy(['nom' => 'humidite_max']);
        $this->assertEquals($humidite_max_valeur_actuelle + $rand_number,$seuils_humidite_max->getValeur());
        $seuils_humidite_max->setValeur($humidite_max_valeur_actuelle);

        $seuils_co2_premier_palier = $seuilRepository->findOneBy(['nom' => 'co2_premier_palier']);
        $this->assertEquals($co2_premier_palier_valeur_actuelle + $rand_number,$seuils_co2_premier_palier->getValeur());
        $seuils_co2_premier_palier->setValeur($co2_premier_palier_valeur_actuelle);

        $seuils_co2_second_palier = $seuilRepository->findOneBy(['nom' => 'co2_second_palier']);
        $this->assertEquals($co2_second_palier_valeur_actuelle + $rand_number,$seuils_co2_second_palier->getValeur());
        $seuils_co2_second_palier->setValeur($co2_second_palier_valeur_actuelle);

        $entityManager->flush();
        $client->request('GET', '/plan/seuils-alertes');

        $crawler = $client->submitForm('submit', [
            'form[temp_min]' => 28,
            'form[temp_max]' => 19,
            'form[humidite_max]' => $humidite_max_valeur_actuelle + $rand_number,
            'form[co2_premier_palier]' => $co2_premier_palier_valeur_actuelle + $rand_number,
            'form[co2_second_palier]' => $co2_second_palier_valeur_actuelle + $rand_number,
        ]);
        $this->assertEquals($temp_min_valeur_actuelle,$seuils_temp_min->getValeur());
        $this->assertEquals($temp_max_valeur_actuelle,$seuils_temp_max->getValeur());
        $this->assertEquals($humidite_max_valeur_actuelle,$seuils_humidite_max->getValeur());
        $this->assertEquals($co2_premier_palier_valeur_actuelle,$seuils_co2_premier_palier->getValeur());
        $this->assertEquals($co2_second_palier_valeur_actuelle,$seuils_co2_second_palier->getValeur());

        $crawler = $client->submitForm('submit', [
            'form[temp_min]' => $temp_min_valeur_actuelle + $rand_number,
            'form[temp_max]' => $temp_max_valeur_actuelle + $rand_number,
            'form[humidite_max]' => $humidite_max_valeur_actuelle + $rand_number,
            'form[co2_premier_palier]' => 1500,
            'form[co2_second_palier]' => 1000,
        ]);
        $this->assertEquals($temp_min_valeur_actuelle,$seuils_temp_min->getValeur());
        $this->assertEquals($temp_max_valeur_actuelle,$seuils_temp_max->getValeur());
        $this->assertEquals($humidite_max_valeur_actuelle,$seuils_humidite_max->getValeur());
        $this->assertEquals($co2_premier_palier_valeur_actuelle,$seuils_co2_premier_palier->getValeur());
        $this->assertEquals($co2_second_palier_valeur_actuelle,$seuils_co2_second_palier->getValeur());

        $crawler = $client->submitForm('submit', [
            'form[temp_min]' => 28,
            'form[temp_max]' => 19,
            'form[humidite_max]' => $humidite_max_valeur_actuelle + $rand_number,
            'form[co2_premier_palier]' => 1500,
            'form[co2_second_palier]' => 1000,
        ]);
        $this->assertEquals($temp_min_valeur_actuelle,$seuils_temp_min->getValeur());
        $this->assertEquals($temp_max_valeur_actuelle,$seuils_temp_max->getValeur());
        $this->assertEquals($humidite_max_valeur_actuelle,$seuils_humidite_max->getValeur());
        $this->assertEquals($co2_premier_palier_valeur_actuelle,$seuils_co2_premier_palier->getValeur());
        $this->assertEquals($co2_second_palier_valeur_actuelle,$seuils_co2_second_palier->getValeur());
    }

    public function test_modifier_salle_cdm_contenu_form():void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/ajouter-salle');

        $client->submitForm('submit', [
            'form[nom]' => 'E789',
            'form[batiment]' => 'Bâtiment P',
            'form[orientation]' => 'No',
            'form[nombreFenetre]' => 2,
            'form[nombrePorte]' => 2,
            'form[contientPc]' => 1
        ]);

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salle = $salleRepository->findOneBy(['nom' => 'E789']);

        $client->request('GET', '/plan/modifier-salle/'.$salle->getId());
        $client->submitForm('submit', [
            'form[nombreFenetre]' => 4,
            'form[nombrePorte]' => 1,
            'form[contientPc]' => 0
        ]);

        $salleRepository = static::getContainer()->get(SalleRepository::class);

        $salle = $salleRepository->findOneBy(['nom' => 'E789']);

        $this->assertEquals(4,$salle->getNombreFenetre());
        $this->assertEquals(1,$salle->getNombrePorte());
        $this->assertFalse($salle->isContientPc());

        $salle = $salleRepository->findOneBy(['nom' => 'E789']);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->remove($salle);
        $entityManager->flush();
    }

    public function test_lister_sa_technicien_lister_tout_sa_montre_etat_capteurs()
    {
        $client = static::createClient();
        $systemeAcquisitionRepository = static::getContainer()->get(SystemeAcquisitionRepository::class);
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister-sa');

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $form = $crawler->filter('form[name="form"]')->form();
        $form['form[choix]'] = '0';

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $listeEtatSA = $crawler->filter('#etat-capteur');
        $listeSA = $systemeAcquisitionRepository->findAll();
        $this->assertSameSize($listeEtatSA, $listeSA);
    }

    public function test_lister_sa_technicien_lister_sa_non_installe_ne_montre_pas_etat_capteurs()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister-sa');

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $form = $crawler->filter('form[name="form"]')->form();
        $form['form[choix]'] = '2';

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $listeEtatSA = $crawler->filter('#etat-capteur');
        $this->assertEmpty($listeEtatSA);
    }
    public function test_lister_sa_technicien_lister_sa_en_installation_ne_montre_pas_etat_capteurs()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister-sa');

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        // select the form of a select element
        $form = $crawler->filter('form[name="form"]')->form();
        $form['form[choix]'] = '1';

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $listeEtatSA = $crawler->filter('#etat-capteur');
        $this->assertEmpty($listeEtatSA);
    }

    public function test_lister_sa_technicien_lister_sa_en_reparation_ne_montre_pas_etat_capteurs()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister-sa');

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        // select the form of a select element
        $form = $crawler->filter('form[name="form"]')->form();
        $form['form[choix]'] = '4';

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $listeEtatSA = $crawler->filter('#etat-capteur');
        $this->assertEmpty($listeEtatSA);
    }

    public function test_lister_sa_technicien_lister_sa_opérationnel_montre_etat_capteurs()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $systemeAcquisitionRepository = static::getContainer()->get(SystemeAcquisitionRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister-sa');

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        // select the form of a select element
        $form = $crawler->filter('form[name="form"]')->form();
        $form['form[choix]'] = '3';

        $client->submit($form);

        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();

        $listeEtatSA = $crawler->filter('#etat-capteur');
        $listeSAOpertationnel = $systemeAcquisitionRepository->findBy(['etat' => 'Opérationnel']);
        $this->assertSameSize($listeSAOpertationnel, $listeEtatSA);
    }

    public function test_retirer_salle_cdm_salle_existente() : void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        $salle = new Salle();
        $salle->setNom("X002");
        $salle->setBatiment("Bâtiment X");
        $salle->setOrientation("No");
        $salle->setNombrePorte(1);
        $salle->setNombreFenetre(6);
        $salle->setContientPc(false);

        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $entityManager->persist($salle);
        $entityManager->flush();

        $salle = $entityManager->getRepository(Salle::class)->findOneBy(['nom' => 'X002']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/retirer-salle/' . $salle->getId());

        // Assertion de rediction vers la page d'accueil
        $this->assertResponseRedirects('/plan', 302);

        $salle = $entityManager->getRepository(Salle::class)->findOneBy(['nom' => 'X002']);
        $this->assertNull($salle);

        $entityManager->flush();
    }

    public function test_retirer_salle_inexistente() : void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/retirer-salle/-1');

        // Assertion de rediction vers la page d'accueil
        $this->assertResponseRedirects('/plan', 302);
    }

    public function test_retirer_sa_existant() : void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $sa = new SystemeAcquisition();
        $sa->setNom("ESP-999");
        $sa->setBaseDonnees("sae34bdk1eq1");
        $sa->setEtat("Opérationnel");

        $salle = new Salle();
        $salle->setNom("X002");
        $salle->setBatiment("Bâtiment X");
        $salle->setOrientation("No");
        $salle->setNombrePorte(1);
        $salle->setNombreFenetre(6);
        $salle->setContientPc(false);
        $salle->setSystemeAcquisition($sa);

        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $entityManager->persist($salle);
        $entityManager->persist($sa);

        $entityManager->flush();

        $sa = $entityManager->getRepository(SystemeAcquisition::class)->findOneBy(['nom' => 'ESP-999']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/retirer-sa/' . $sa->getId());

        // Assertion de rediction vers la page d'accueil
        $this->assertResponseRedirects('/plan/lister-sa', 302);

        $sa = $entityManager->getRepository(SystemeAcquisition::class)->findOneBy(['nom' => 'ESP-999']);
        $this->assertNull($sa);

        $entityManager->remove($salle);
        $entityManager->flush();
    }

    public function test_retirer_sa_inexistant() : void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/retirer-sa/-1');

        // Assertion de rediction vers la page d'accueil
        $this->assertResponseRedirects('/plan/lister-sa', 302);
    }

    public function test_liste_demande_travaux_technicien_commentaire_present(){
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);

        $demandeTravauxRepository = static::getContainer()->get(DemandeTravauxRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $demande = $demandeTravauxRepository->findOneBy([
            'type' => 'Installation',
            'terminee' => false
        ]);

        $crawler = $client->request('GET', '/plan/demande-travaux/' . $demande->getId());

        $commentaire = $crawler->filter('#commentaire');

        $this->assertNotNull($commentaire);
    }

    public function test_demande_reparation_technicien_sa_valide(){
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $systemeAcquisitionRepository = static::getContainer()->get(SystemeAcquisitionRepository::class);

        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);
        $sa = new SystemeAcquisition();
        $sa->setNom("ESP-999");
        $sa->setBaseDonnees("sae34bdk1eq1");
        $sa->setEtat("Opérationnel");

        $salle = new Salle();
        $salle->setNom("X002");
        $salle->setBatiment("Bâtiment X");
        $salle->setOrientation("No");
        $salle->setNombrePorte(1);
        $salle->setNombreFenetre(6);
        $salle->setContientPc(false);
        $salle->setSystemeAcquisition($sa);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($salle);
        $entityManager->persist($sa);
        $entityManager->flush();

        $sa = $systemeAcquisitionRepository->findOneBy(['nom' => 'ESP-999']);
        $salle = $sa->getSalle();

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/signaler-erreur/' . $salle->getId());

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form['form[commentaire]'] = 'Erreur de test';
        $form['form[emailDemandeur]'] = 'jmalki@exemple.com';

        $client->submit($form);

        $this -> assertResponseRedirects('/plan/lister-sa', 302);

        $sql = "DELETE FROM salle WHERE nom = 'X002'";
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        $sql = "DELETE FROM systeme_acquisition WHERE nom = 'ESP-999'";
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();
    }

    public function test_demande_reparation_technicien_sa_invalide()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);

        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);
        $client->loginUser($testUser);

        $sa = new SystemeAcquisition();
        $sa->setNom("ESP-999");
        $sa->setBaseDonnees("sae34bdk1eq1");
        $sa->setEtat("Non installé");

        $salle = new Salle();
        $salle->setNom("X002");
        $salle->setBatiment("Bâtiment X");
        $salle->setOrientation("No");
        $salle->setNombrePorte(1);
        $salle->setNombreFenetre(6);
        $salle->setContientPc(false);
        $salle->setSystemeAcquisition($sa);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($salle);
        $entityManager->persist($sa);
        $entityManager->flush();

        $client->request('GET', '/signaler-erreur/' . $salle->getId() . "/1");

        $this->assertResponseRedirects('/plan/lister-sa', 302);
        $this->assertResponseStatusCodeSame(302);

        $sa->setEtat("Réparation");
        $entityManager->persist($sa);
        $entityManager->flush();

        $crawler = $client->request('GET', '/signaler-erreur/' . $salle->getId() . "/1");
        $this->assertResponseIsSuccessful();


        $sa->setEtat("Installation");
        $entityManager->persist($sa);
        $entityManager->flush();

        $client->request('GET', '/signaler-erreur/' . $salle->getId() . "/1");
        $this->assertResponseIsSuccessful();


        $sql = "DELETE FROM salle WHERE nom = 'X002'";
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();

        $sql = "DELETE FROM systeme_acquisition WHERE nom = 'ESP-999'";
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();
    }
}