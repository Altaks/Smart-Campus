<?php

namespace App\tests\Controller\Infra\Technicien;

use App\Repository\UtilisateurRepository;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\ReleveService;

class systemeAcquisitionTechnicienTest extends WebTestCase
{
    public function test_controleur_infra_route_infra_systemes_acquisition_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/technicien/systemes-acquisition');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', "Liste des systèmes d'acquisition");
        $h2 = $crawler->filter("h2");
        $this->assertEquals("Systèmes d'acquisition fonctionnels", $h2->eq(0)->text());
    }

    public function test_controleur_infra_route_infra_systemes_acquisition_est_fonctionnel()
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/technicien/systemes-acquisition');
        $this->assertResponseIsSuccessful();

        $tr = $crawler->filter('.th-fonctionnel');

        for($i = 0; $i < $tr->count(); $i++) {
            $tag = $tr->eq($i)->text();
            $releve = getDernier(intval($tag));

            $dateReleve = new DateTime($releve["date"]);
            $date = new DateTime('2023-12-13 11:00:00');
            $dateDiff = $date->diff($dateReleve);

            $this->assertLessThan(6,$dateDiff->i);
            $this->assertNotEquals(null,$releve["co2"]);
            $this->assertNotEquals(null,$releve["hum"]);
            $this->assertNotEquals(null,$releve["temp"]);
        }
    }

    public function test_controleur_infra_route_infra_systemes_acquisition_requete_en_tant_que_charge_de_mission(){
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/technicien/systemes-acquisition');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }
}