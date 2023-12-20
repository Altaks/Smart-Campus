<?php

namespace App\tests\Controller\Public\CdM;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class chargeDeMissionTest extends WebTestCase
{
    public function test_envoi_formulaire_de_connexion_cas_valide_charge_de_mission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testChargeDeMission',
            '_password' => 'testChargeDeMission'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/auth-Success$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
        $this->assertMatchesRegularExpression('/\/accueil\/charge\-de\-mission$/', $client->getResponse()->headers->get('location'));
    }

    public function test_envoi_formulaire_de_connexion_cas_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testChargeDeMission',
            '_password' => 'motDePasseInvalide'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
    }
}
