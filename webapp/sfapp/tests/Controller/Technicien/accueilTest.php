<?php

namespace App\tests\Controller\Accueil;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class accueilTechnicienTest extends WebTestCase
{
    public function test_controleur_techniciencontroller_route_accueil_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/tech');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Bienvenue Technicien');
        $h2 = $crawler->filter("h2");
        $this->assertEquals('Relevés', $h2->eq(0)->text());
        $this->assertEquals('Infrastructure',$h2->eq(1)->text());
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_controleur_techniciencontroller_route_accueil_requete_utilisateur_pas_connecte(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/accueil/tech');

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    public function test_controleur_techniciencontroller_route_accueil_requete_en_tant_que_charge_de_mission(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/tech');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_controleur_techniciencontroller_route_accueil_lien_releve(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/tech');
        $this->assertResponseIsSuccessful();

        $releve_link = $crawler->filter('a#releves')->link();
        $this->assertStringEndsWith('/releves', $releve_link->getUri());
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_controleur_techniciencontroller_route_accueil_lien_infrastructure(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/tech');
        $this->assertResponseIsSuccessful();

        $infra_link = $crawler->filter('a#infra')->link();
        $this->assertStringEndsWith('/infra/tech', $infra_link->getUri());
    }
}
