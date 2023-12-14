<?php

namespace App\Tests\Controller\Infra\ChargeDeMission;

use App\Repository\BatimentRepository;
use App\Repository\SalleRepository;
use App\Repository\SystemeAcquisitionRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function PHPUnit\Framework\assertEquals;

class salleAjouterSaTest extends WebTestCase
{
    public function test_controleur_infracontroller_route_infra_charge_de_mission_salle_ajouter_sa_requete_en_tant_que_charge_de_mission(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $batimentRepository = static::getContainer()->get(BatimentRepository::class);
        $batimentD = $batimentRepository->findOneBy(['nom' => 'Batiment D']);

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salleD304 = $salleRepository->findOneBy(['batiment' => $batimentD->getId(), 'nom' => 'D304']);


        $crawler = $client->request('GET', '/infra/charge-de-mission/salles/' . $salleD304->getId() . '/ajouter-sa');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Ajouter un système d\'acquisition à la salle D304');
        $this->assertSelectorCount(1,'form');
        $this->assertSelectorTextContains('label', 'Sélectionner le système d\'acquisition :');
        $this->assertSelectorCount(1,'select');
        $this->assertSelectorTextContains('button','Ajouter');
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_controleur_infracontroller_route_infra_charge_de_mission_salle_ajouter_sa_requete_utilisateur_pas_connecte(): void
    {
        $client = static::createClient();

        $batimentRepository = static::getContainer()->get(BatimentRepository::class);
        $batimentD = $batimentRepository->findOneBy(['nom' => 'Batiment D']);

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salleD304 = $salleRepository->findOneBy(['batiment' => $batimentD->getId(), 'nom' => 'D304']);

        $crawler = $client->request('GET', '/infra/charge-de-mission/salles/' . $salleD304->getId() . '/ajouter-sa');

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
    }

    // Test à modifier en fonction des resultats obtenue
    public function test_controleur_infracontroller_route_infra_charge_de_mission_salle_ajouter_sa_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testTechnicien']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $batimentRepository = static::getContainer()->get(BatimentRepository::class);
        $batimentD = $batimentRepository->findOneBy(['nom' => 'Batiment D']);

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salleD304 = $salleRepository->findOneBy(['batiment' => $batimentD->getId(), 'nom' => 'D304']);

        $crawler = $client->request('GET', '/infra/charge-de-mission/salles/' . $salleD304->getId() . '/ajouter-sa');

        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_controleur_infracontroller_route_infra_charge_de_mission_salle_ajouter_sa_envoyer_formulaire(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'testChargeDeMission']);

        // simulate $testUser being logged in²
        $client->loginUser($testUser);

        $batimentRepository = static::getContainer()->get(BatimentRepository::class);
        $batimentD = $batimentRepository->findOneBy(['nom' => 'Batiment D']);

        $salleRepository = static::getContainer()->get(SalleRepository::class);
        $salleD304 = $salleRepository->findOneBy(['batiment' => $batimentD->getId(), 'nom' => 'D304']);

        $saRepository = static::getContainer()->get(SystemeAcquisitionRepository::class);
        $saSansSalle = $saRepository->findOneBy(['adresseMac' => "00:00:00:00:00:04"]);

        $crawler = $client->request('GET', '/infra/charge-de-mission/salles/'. $salleD304->getId() .'/ajouter-sa');

        $crawler = $client->submitForm('form_submit', [
            'form[sa]' => $saSansSalle->getId(),
        ]);

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/infra\/charge-de-mission\/salles\/$/', $client->getResponse()->headers->get('location'));
    }
}
