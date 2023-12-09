<?php

namespace App\Tests\Controller\Connexion;

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
        $this->assertSelectorCount(2, 'input');
        $this->assertSelectorTextContains('button', 'Se connecter');

    }
}
