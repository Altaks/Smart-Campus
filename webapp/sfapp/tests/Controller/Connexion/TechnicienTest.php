<?php

namespace App\tests\Controller\Connexion;

use PHPUnit\Framework\TestCase;

class TechnicienTest extends TestCase
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
        $client->followRedirect();
        $this->assertEquals( $crawler->text(), "Redirecting to http://localhost/auth-Success Redirecting to http://localhost/auth-Success.");
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
        $client->followRedirect();
        $this->assertEquals( $crawler->text(), "Redirecting to http://localhost/connexion Redirecting to http://localhost/connexion.");
    }
}
