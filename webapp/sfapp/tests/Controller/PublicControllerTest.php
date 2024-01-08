<?php

namespace App\tests\Controller;
use App\Repository\DemandeTravauxRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
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
    }

    public function test_envoi_formulaire_de_connexion_cas_valide_charge_de_mission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'yghamri',
            '_password' => 'pwd-yghamri'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/accueil\/$/', $client->getResponse()->headers->get('location'));
    }

    public function test_envoi_formulaire_de_connexion_cas_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'yghamri',
            '_password' => 'motDePasseInvalide'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
    }

    public function test_envoi_formulaire_de_connexion_cas_valide_technicien(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'jmalki',
            '_password' => 'pwd-jmalki'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/accueil\/$/', $client->getResponse()->headers->get('location'));
    }

    public function test_envoi_formulaire_de_connexion_cas_invalide_technicien(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $crawler = $client->submitForm('submit', [
            '_username' => 'jmalki',
            '_password' => 'motDePasseInvalide'
        ]);
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression('/\/connexion$/', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
    }

    public function test_accueil_technicien_requete_en_tant_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/accueil/');
        $this->assertResponseIsSuccessful();


        $demandesRepository = static ::getContainer()->get(DemandeTravauxRepository::class);
        $nbDemandes = count($demandesRepository->findBy(["type"=>"Installation", "terminee"=>false]));


        $li = $crawler->filter("#demandeInstallation");
        $this->assertEquals($nbDemandes, $li->count());
    }
}