<?php

namespace App\tests\Controller;
use App\DataFixtures\Test\RelevesFixtures;
use App\Repository\DemandeTravauxRepository;
use App\Repository\UtilisateurRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

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
        $nbDemandes = sizeof($demandesRepository->findBy(["type"=>"Installation", "terminee"=>false]));


        $li = $crawler->filter("#listeDemandesInstallation")->filter('li');
        $this->assertEquals($nbDemandes, $li->count());
    }

    public function test_accueil_requette_sans_utilisateur_connecte(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/accueil');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenu sur le site Smart Campus');
    }

    public function test_releve_requette_sans_utilisateur_connecte(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/releves');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Veuillez choisir une salle');
    }

    public function test_releve_requette_sans_utilisateur_connecte_choisir_une_salle(): void
    {
        $this->databaseTool->loadFixtures([
            RelevesFixtures::class
        ]);

        $client = static::createClient();
        $sallesRepository = static ::getContainer()->get(SalleRepository::class);
        $salle = $sallesRepository->findOneBy(['nom' => 'C004']);
        $crawler = $client->request('POST', '/releves',['salle' => $salle->getNom()]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Relevés de la salle '.$salle->getNom());

    }
}