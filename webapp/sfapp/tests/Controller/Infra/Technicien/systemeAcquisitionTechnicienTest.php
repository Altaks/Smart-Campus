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

    public function test_controleur_infra_route_infra_systemes_acquisition_est_non_fonctionnel()
    {
        $client = static::createClient();
        $service = new ReleveService();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/technicien/systemes-acquisition');
        $this->assertResponseIsSuccessful();

        $th = $crawler->filter('.th-non-fonctionnel');

        for($i = 0; $i < $th->count(); $i++) {
            $tag = $th->eq($i)->text();
            $releve = $service->getDernier(intval($tag));

            $currDate = new DateTime();
            $sysDate = new DateTime($releve["date"]);
            $diff = $currDate->diff($sysDate);

            $this->assertNotEquals(null, $releve["date"]);
            $this->assertLessThan(6, $diff->i);
            $this->assertTrue(is_null($releve["co2"]) || is_null($releve["temp"]) || is_null($releve["hum"]));
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