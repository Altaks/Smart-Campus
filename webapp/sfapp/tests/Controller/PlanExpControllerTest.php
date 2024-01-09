<?php

namespace App\tests\Controller;

use App\Entity\SystemeAcquisition;
use App\Repository\DemandeTravauxRepository;
use App\Repository\SalleRepository;
use App\Repository\SystemeAcquisitionRepository;
use App\Entity\Salle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UtilisateurRepository;
use function PHPUnit\Framework\assertEquals;


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
            $systemeAcquisition->setBaseDonnees("sae34bdm2eq3");
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
    }

    public function test_tech_declarer_operationnel_route():void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $travauxRepository = static::getContainer()->get(DemandeTravauxRepository::class);
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

        }
    }

    public function test_ajouter_salle_cdm_contenu_form():void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/plan/ajouter-salle/');
        $nbLabel = count($crawler->filter('label'));
        $this->assertEquals(6,$nbLabel);

        $client->submitForm('submit', [
            'salle' => 'E789',
            'batiment' => 'Bâtiment P',
            'orientation' => 'No',
            'fenetre' => 2,
            'porte' => 2,
            'ordinateur' => false
        ]);
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salle = $salleRepository->findOneBy(['nom' => 'E789']);
        $this->assertNotEmpty($salle);
        if(!empty($salle)) {
            $entityManager->remove($salle);
            $entityManager->flush();
        }

        $client->submitForm('submit', [
            'salle' => 'D302',
            'batiment' => 'Bâtiment D',
            'orientation' => 'Su',
            'fenetre' => 6,
            'porte' => 2,
            'ordinateur' => true
        ]);

        $nbSalleD302 = count($salleRepository->findBy(['nom' => 'D302']));
        $this->assertEquals(1,$nbSalleD302);
    }
}