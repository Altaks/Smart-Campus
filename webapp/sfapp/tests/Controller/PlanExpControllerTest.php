<?php

namespace App\tests\Controller;

use App\Entity\SystemeAcquisition;
use App\Entity\Salle;
use App\Repository\DemandeTravauxRepository;
use App\Repository\SalleRepository;
use App\Repository\SystemeAcquisitionRepository;
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
            $systemeAcquisition->setBaseDonnees("sae34bdm2eq1");
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
        $travaux = $travauxRepository->findAll();

        for ($i = 0; $i < count($travaux); $i++) {
            $crawler = $client->request('GET', '/plan/demande-travaux/' . $travaux[$i]->getId());

            $option = $crawler->filter("#option")->filter('option');

            if ($travaux[$i]->getSystemeAcquisition() == null) {
                $this->assertEquals($nbSystemesAcquisitionNonInstalle + 1, $option->count());
            }
            else {
                $this->assertEquals($nbSystemesAcquisitionNonInstalle + 2, $option->count());
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
        $client->request('GET', '/plan/lister_sa');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_lister_SA_route_connexion_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister_sa');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_ajouter_sa_technicien_connexion_valide_technicien()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/ajouter_sa');
        $this->assertResponseIsSuccessful();
    }

    public function test_ajouter_sa_technicien_connexion_invalide_charge_de_mission()
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/ajouter_sa');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());

    }

    public function test_ajouter_sa_technicien_connexion_invalide_usager()
    {
        $client = static::createClient();
        $client->request('GET', '/plan/ajouter_sa');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_ajouter_sa_technicien_verifiacation_formulaire()
    {
        $client = static::createClient();
        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'jmalki']);
        $this->assertNotNull($utilisateur);

        // simulate $testUser being logged in
        $client->loginUser($utilisateur);

        $client->request('GET', '/plan/ajouter_sa');
        $this->assertResponseIsSuccessful();

        $crawler = $client->submitForm('submit', [
            'form[nom]' => 'ESP-999',
            'form[baseDonnees]' => 'sae34bdl1eq1'
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
            'form[nom]' => 'D302',
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
            'form[nombrePorte]' => 2,
            'form[contientPc]' => 1
        ]);

        $salle = $salleRepository->findOneBy(['nom' => 'E789']);

        $this->assertEquals(4,$salle->getNombreFenetre());

        $client->request('GET', '/plan/modifier-salle/'.$salle->getId());
        $client->submitForm('submit', [
            'form[nombreFenetre]' => 4,
            'form[nombrePorte]' => 1,
            'form[contientPc]' => 1
        ]);

        $salle = $salleRepository->findOneBy(['nom' => 'E789']);

        $this->assertEquals(1,$salle->getNombrePorte());

        $client->request('GET', '/plan/modifier-salle/'.$salle->getId());
        $client->submitForm('submit', [
            'form[nombreFenetre]' => 4,
            'form[nombrePorte]' => 1,
            'form[contientPc]' => 0
        ]);

        $salle = $salleRepository->findOneBy(['nom' => 'E789']);

        $this->assertEquals(0,$salle->getContientPc());

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->remove($salle);
        $entityManager->flush();
    }
}