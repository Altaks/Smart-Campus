<?php

namespace App\Tests\Controller\Infra\ChargeDeMission;

use App\Controller\InfraController;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class systemesAcquisitionTest extends WebTestCase
{
    public function test_controller_infra_existe(): void
    {
        $this->assertTrue(class_exists(InfraController::class));
    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_existe_en_tant_que_charge_mission() : void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');
        $this->assertResponseIsSuccessful();
    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_contient_titre() : void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');

        $this->assertSelectorTextContains('h1', 'Liste des systèmes d\'acquisition');
    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_contient_deux_tableaux() : void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');

        $this->assertSelectorCount(2,'table');
    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_contient_bons_sous_titres() : void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');

        $h2 = $crawler->filter("h2");

        $this->assertSame("Systèmes d'acquisition fonctionnels",$h2->eq(0)->text());
        $this->assertSame("Systèmes d'acquisition non fonctionnels",$h2->eq(1)->text());
    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_contient_tableaux_avec_bons_entetes() : void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');

        $th = $crawler->filter("th");
            $this->assertSame("Tag", $th->eq(0)->text());
            $this->assertSame("Bâtiment", $th->eq(1)->text());
            $this->assertSame("Salle", $th->eq(2)->text());

    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_requete_sans_etre_connecte() : void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    public function test_page_infra_charge_de_mission_liste_systemes_acquisition_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/systemes-acquisition');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }
}
