<?php

namespace App\Tests\Controller\ChargeMission;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

class accueilTest extends WebTestCase
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

        $this->assertSelectorTextContains('h1', 'Bienvenue Charge de mission');
        $h2 = $crawler->filter("h2");
        $this->assertEquals( 'Relevés', $h2->eq(0)->text());
        $this->assertEquals('Infrastructures',$h2->eq(1)->text());
    }

    // Test a modifier en fonction des resultats obtenue
    public function test_controleur_chargemissioncontroller_route_accueil_requete_utilisateur_pas_connecte(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/accueil/charge-de-mission');

        $this->assertResponseStatusCodeSame(401, $client->getResponse()->getStatusCode());
        $this->assertEquals("Unauthorized",$crawler->text());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    // Test a modifier en fonction des resultats obtenue
    public function test_controleur_chargemissioncontroller_route_accueil_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/charge-de-mission');

        $this->assertResponseStatusCodeSame(401, $client->getResponse()->getStatusCode());
        $this->assertEquals("Unauthorized",$crawler->text());
        $this->assertMatchesRegularExpression('/\/accueil/tech$/', $client->getResponse()->headers->get('location'));
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

        $client->clickLink('Relevés');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesRegularExpression('/\/releve$/', $client->getResponse()->headers->get('location'));
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

        $client->clickLink('Infrastructures');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesRegularExpression('/\/infra/charge-de-mission$/', $client->getResponse()->headers->get('location'));
    }
}
