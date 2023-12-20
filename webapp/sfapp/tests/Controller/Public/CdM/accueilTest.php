<?php

namespace App\Tests\Controller\Accueil;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

class accueilChargeDeMissionTest extends WebTestCase
{

    public function test_controleur_chargemissioncontroller_route_accueil_requete_en_tant_que_charge_de_mission(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/charge-de-mission');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Bienvenue Chargé de mission');
        $h2 = $crawler->filter("h2");
        $this->assertEquals('Relevés', $h2->eq(0)->text());
        $this->assertEquals('Infrastructure',$h2->eq(1)->text());
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_controleur_chargemissioncontroller_route_accueil_requete_utilisateur_pas_connecte(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/accueil/charge-de-mission');

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_controleur_chargemissioncontroller_route_accueil_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/charge-de-mission');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_controleur_chargemissioncontroller_route_accueil_lien_releve(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/charge-de-mission');
        $this->assertResponseIsSuccessful();

        $releve_link = $crawler->filter('a#releves')->link();
        $this->assertStringEndsWith('/releves', $releve_link->getUri());
    }

    public function test_controleur_chargemissioncontroller_route_accueil_lien_infrastructure(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/charge-de-mission');
        $this->assertResponseIsSuccessful();

        $infra_link = $crawler->filter('a#infra')->link();
        $this->assertStringEndsWith('/infra/charge-de-mission', $infra_link->getUri());
    }
}
