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

    public function test_envoi_formulaire_de_connexion_cas_identifiant_invalide(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'identifiantInvalide',
            '_password' => 'MotDePasseLambda'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
        $this->assertEquals( $crawler->text(), "Redirecting to http://localhost/connexion Redirecting to http://localhost/connexion.");
    }
}
