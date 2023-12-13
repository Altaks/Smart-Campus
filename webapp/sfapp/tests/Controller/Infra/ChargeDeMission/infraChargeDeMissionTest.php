<?php

namespace App\tests\Controller\ChargeMission;

use App\Controller\InfraController;
use App\Entity\Batiment;
use App\Repository\BatimentRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UtilisateurRepository;

class infraChargeDeMissionTest extends WebTestCase
{

    public function test_controller_infra_existe() : void
    {
        $this->assertTrue(class_exists(InfraController::class));
    }

    public function test_page_ajouter_batiment_en_tant_que_charge_de_mission_existe(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');
        $this->assertResponseIsSuccessful();
    }

    public function test_page_ajouter_batiment_contient_titre() : void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains("h1", "Ajouter un bâtiment");
    }

    public function test_page_ajouter_batiment_contient_formulaire() : void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists("form");
    }

    public function test_page_ajouter_batiment_contient_formulaire_en_post() : void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter("form")->form();
        $this->assertSame($form->getMethod(), "POST");
    }

    public function test_page_ajouter_batiment_contient_formulaire_avec_nom_batiment() : void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');
        $this->assertResponseIsSuccessful();

        $labels = $crawler->filter("label");
        $this->assertSame("Nom du bâtiment", $labels->eq(0)->text());
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_page_ajouter_batiment_sans_etre_connecte(): void
    {
        $client = static::createClient();

        $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_page_ajouter_batiment_en_etant_connecte_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/batiments/ajouter');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }
}
