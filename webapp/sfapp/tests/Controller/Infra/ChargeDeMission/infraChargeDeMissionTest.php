<?php

namespace App\tests\Controller\Infra\ChargeDeMission;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class infraChargeDeMissionTest extends WebTestCase
{
    public function test_controleur_infra_route_infra_requete_en_tant_que_charge_de_mission(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/');
        $this->assertResponseIsSuccessful();

        $h2 = $crawler->filter("h2");
        $this->assertEquals('Bâtiments', $h2->eq(0)->text());
        $this->assertEquals('Salles',$h2->eq(1)->text());
        $this->assertEquals("Systèmes d'acquisition",$h2->eq(2)->text());
    }

    public function test_controleur_infra_route_infra_lien_batiments(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/');
        $this->assertResponseIsSuccessful();

        $infra_link = $crawler->filter('a#batiments')->link();
        $this->assertStringEndsWith('/infra/charge-de-mission/batiments', $infra_link->getUri());
    }

    public function test_controleur_infra_route_infra_lien_salles(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/');
        $this->assertResponseIsSuccessful();

        $infra_link = $crawler->filter('a#salles')->link();
        $this->assertStringEndsWith('/infra/charge-de-mission/salles', $infra_link->getUri());
    }

    public function test_controleur_infra_route_infra_lien_systemes_d_acquisition(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/');
        $this->assertResponseIsSuccessful();

        $infra_link = $crawler->filter('a#systemesacquisition')->link();
        $this->assertStringEndsWith('/infra/charge-de-mission/systemes-acquisition', $infra_link->getUri());
    }

    public function test_controleur_infra_route_infra_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }
}