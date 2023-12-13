<?php

namespace App\tests\Controller\Infra\ChargeDeMission;

use App\Controller\InfraController;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class batimentTest extends WebTestCase
{

    public function test_controller_infra_existe(): void
    {
        $this->assertTrue(class_exists(InfraController::class));
    }

    public function test_page_infra_liste_batiments_existe_en_tant_que_charge_mission() : void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/infra/charge-de-mission/batiments');
        $this->assertResponseIsSuccessful();
    }

    public function test_page_liste_batiment_contient_titre() : void
    {
        $client = static::createClient();

        $client->request('GET', '/infra/charge-de-mission/batiments');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Liste des Bâtiments');
    }

    public function test_page_liste_batiment_contient_liste() : void
    {
        $client = static::createClient();

        $client->request('GET', '/infra/charge-de-mission/batiments');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('table');
    }

    public function test_page_liste_batiment_contient_liste_avec_bon_titres() : void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/infra/charge-de-mission/batiments');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('table');

        $headers = $crawler->filter("thead");

        $this->assertSelectorTextContains("Nom du bâtiment", $headers->eq(0)->text());
        $this->assertSelectorTextContains("Nombre de Salle", $headers->eq(1)->text());
    }

    public function test_page_liste_batiment_contient_lien_nouveau_bâtiment() : void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/infra/charge-de-mission/batiments');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('table');

        $links = $crawler->filter("a");

        $contientLienVersAjouterBatiment = false;
        foreach ($links as $link ){
            if(strcmp($link, "/infra/batiments/ajouter")) {
                $contientLienVersAjouterBatiment = true;
                break;
            }
        }

        $this->assertTrue($contientLienVersAjouterBatiment);
    }

}
