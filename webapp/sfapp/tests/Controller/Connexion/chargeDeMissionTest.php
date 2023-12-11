<?php

namespace App\Tests\Controller\Connexion;

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
        $client->followRedirect();
        $this->assertEquals( $crawler->text(), "Redirecting to http://localhost/auth-Success Redirecting to http://localhost/auth-Success.");
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
        $client->followRedirect();
        $this->assertEquals( $crawler->text(), "Redirecting to http://localhost/connexion Redirecting to http://localhost/connexion.");
    }
}
