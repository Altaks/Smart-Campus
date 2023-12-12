<?php

namespace App\tests\Controller\Connexion;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class technicienTest extends WebTestCase
{

    public function test_envoi_formulaire_de_connexion_cas_valide_technicien(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testTechnicien',
            '_password' => 'testTechnicien'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/auth-Success$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
        $this->assertMatchesRegularExpression('/\/accueil\/tech$/', $client->getResponse()->headers->get('location'));
    }

    public function test_envoi_formulaire_de_connexion_cas_invalide_technicien(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'testTechnicien',
            '_password' => 'motDePasseInvalide'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
    }
}
