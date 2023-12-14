<?php

namespace App\tests\Controller\Infra\ChargeDeMission;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class sallesTest extends WebTestCase
{
    public function test_controleur_infracontroller_route_infra_charge_de_mission_salles_requete_en_tant_que_charge_de_mission(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/salles');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Liste des salles');
        $th = $crawler->filter("th");
        $this->assertEquals( 'Bâtiment', $th->eq(0)->text());
        $this->assertEquals('Nom de la salle',$th->eq(1)->text());
        $this->assertEquals('Etage',$th->eq(2)->text());
        $this->assertEquals('Orientation',$th->eq(3)->text());
        $this->assertEquals('Nombre de porte',$th->eq(4)->text());
        $this->assertEquals('Nombre de fenêtre',$th->eq(5)->text());
        $this->assertEquals('Contient des PC',$th->eq(6)->text());
        $this->assertEquals('Système d\'acquisition',$th->eq(7)->text());
        /*$a = $crawler->filter("a");
        for($i = 0; $i < 3; $i++){
            $this->assertNotEquals('Salles',$a->eq($i)->text());
        }*/
    }

    // Test a modifier en fonction des resultats obtenue
    public function test_controleur_infracontroller_route_infra_charge_de_mission_salles_requete_utilisateur_pas_connecte(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/infra/charge-de-mission/salles');

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());

        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    // Test a modifier en fonction des resultats obtenue
    public function test_controleur_infracontroller_route_infra_charge_de_mission_salles_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/charge-de-mission/salles');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }
}
