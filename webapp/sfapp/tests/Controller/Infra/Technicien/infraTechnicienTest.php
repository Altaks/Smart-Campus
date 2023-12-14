<?php

namespace App\Tests\Controller\Infra;

use App\Controller\InfraController;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class infraTechnicienTest extends WebTestCase{
    public function test_controleur_Infra_route_infra_requete_en_tant_que_technicien(): void
    {
        $this->assertTrue(class_exists(InfraController::class));

        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/infra/technicien/');
        $this->assertResponseIsSuccessful();

        $a = $crawler->filter("a");
        $this->assertStringEndsWith("/infra/technicien/batiments", $a->eq(0)->link()->getUri());
        $this->assertStringEndsWith("/infra/technicien/salles", $a->eq(1)->link()->getUri());
        $this->assertStringEndsWith("/infra/technicien/systemes-acquisition", $a->eq(2)->link()->getUri());
    }
}

?>