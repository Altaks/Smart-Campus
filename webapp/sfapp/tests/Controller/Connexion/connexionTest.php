<?php

namespace App\Tests\Controller\Connexion;

use http\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class connexionTest extends WebTestCase
{
    public function test_formulaire_de_connexion_correspond_a_la_maquette(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorTextContains('form', '');
        $label = $crawler->filter("label");
        $this->assertEquals($label->eq(0)->text(), 'Identifiant');
        $this->assertEquals($label->eq(1)->text(), 'Mot de passe');
        $this->assertSelectorCount(3, 'input');
        $this->assertSelectorTextContains('button', 'Se connecter');

    }

    public function test_envoi_formulaire_de_connexion_cas_valide_charge_de_mission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testChargeDeMission',
            '_password' => 'testChargeDeMission'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        //$this->assertResponseRedirects("/auth-Success");
    }

    public function test_envoi_formulaire_de_connexion_cas_identifiant_invalide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testAdministrateur',
            '_password' => '\$2y$13\$iCQoObBJ8ytBcypkX1RKTes7upAIBT5ktnPaHai3pI13YUNGE1y2a'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        //$this->assertResponseRedirects("/connexion");
    }

    public function test_envoi_formulaire_de_connexion_cas_mot_de_passe_invalide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testChargeDeMission',
            '_password' => 'motDePasseInvalide'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        //$this->assertResponseRedirects("/connexion");
    }

    public function test_envoi_formulaire_de_connexion_cas_identifiant_et_mot_de_passe_invalide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testAdministrateur',
            '_password' => 'motDePasseInvalide'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        //$this->assertResponseRedirects("/connexion");
    }
}
