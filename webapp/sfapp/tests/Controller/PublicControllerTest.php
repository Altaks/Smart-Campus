<?php

namespace App\tests\Controller;
use App\DataFixtures\Test\RelevesFixtures;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use App\Repository\DemandeTravauxRepository;
use App\Repository\SalleRepository;
use App\Repository\SystemeAcquisitionRepository;
use App\Repository\UtilisateurRepository;
use App\Service\ReleveService;
use App\Service\SeuilService;
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

    public function test_accueil_requette_sans_utilisateur_connecte(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/accueil/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur le site Smart Campus');
    }

    public function test_releve_requette_sans_utilisateur_connecte(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/releves');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Veuillez choisir une salle');
    }

     /*public function test_releves_alertes_et_conseils(): void
    {
        $client = static::createClient();

        $salleRepository = static ::getContainer()->get(SalleRepository::class);
        $salle = $salleRepository->findOneBy(['nom' => 'D206']);

        $crawler = $client->request('POST', '/releves', [
            'form[salle]' => $salle->getId()
        ]);

        $crawler = $client->submitForm('submit', [
            'form[salle]' => $salle->getId()
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertEquals(0,$crawler->filter('form')->count());

        $conseilTemp = $crawler->filter('#conseilTemp');
        $conseilHum = $crawler->filter('#conseilHum');
        $conseilQual = $crawler->filter('#conseilQual');
        $alerteTemp = $crawler->filter('#alerteTemp');
        $alerteHum = $crawler->filter('#alerteHum');
        $alerteQual = $crawler->filter('#alerteQual');

        $seuilService = new SeuilService();
        $seuils = $seuilService->getSeuils($client->getContainer()->get('doctrine'));

        $relevesService = new  ReleveService();
        $relevesTemp = $relevesService->getDernier($salle->getSystemeAcquisition(),'temp')['valeur'];
        $relevesHum = $relevesService->getDernier($salle->getSystemeAcquisition(),'hum')['valeur'];
        $relevesQual = $relevesService->getDernier($salle->getSystemeAcquisition(),'co2')['valeur'];

        #if ($conseilTemp->count() != 0 && $alerteTemp->count() != 0) {
            if ($seuils['temp_min'] > $relevesTemp) {
                $this->assertEquals('Il est conseillé de monter le chauffage et de fermer les fenêtres', $conseilTemp->text());
                $this->assertEquals('La température de la salle est trop basse', $alerteTemp->text());
            }
            else if ($seuils['temp_max'] < $relevesTemp) {
                $this->assertEquals('Il est conseillé de baisser le chauffage et d\'ouvrir les fenêtres', $conseilTemp->text());
                $this->assertEquals('La température de la salle est trop élevé', $alerteTemp->text());
            }
        #}
        else {
            $this->assertGreaterThan($seuils['temp_min'], $relevesTemp);
            $this->assertLessThan($seuils['temp_max'], $relevesTemp);
        }

        if ($conseilHum->count() != 0) {
            if ($seuils['humidite_max'] < $relevesHum) {
                $this->assertEquals('Il est conseillé de fermer les fenêtres', $conseilHum->text());
                $this->assertEquals('Le niveau d\'humidité de la salle et trop élevé', $alerteHum->text());
            }
        }
        else {
            $this->assertLessThan($seuils['humidite_max'], $relevesHum);
        }

        if ($conseilQual->count() != 0) {
            if ($seuils['co2_premier_palier'] < $relevesQual) {
                $this->assertEquals('Il est conseillé d\'aérer la salle', $conseilQual->text());

                if ($seuils['co2_second_palier'] > $relevesQual) {
                    $this->assertEquals('La qualité de l\'air est mauvaise', $alerteQual->text());
                }
                else {
                    $this->assertEquals('La qualité de l\'air est très mauvaise', $alerteQual->text());
                }
            }
        }
        else {
            $this->assertLessThan($seuils['co2_premier_palier'], $relevesQual);
        }
    }*/

    public function test_signaler_erreur_requette_avec_utilisateur_connecte_en_temps_que_technicien(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $saRepository = static ::getContainer()->get(SystemeAcquisitionRepository::class);
        $saTest = new SystemeAcquisition();
        $saTest->setNom("ESP-000");
        $saTest->setEtat("Opérationnel");
        $saTest->setBaseDonnees("sae34bdk1eq1");


        $salleTest = new Salle();
        $salleTest->setNom("X000");
        $salleTest->setBatiment("X");
        $salleTest->setOrientation("NE");
        $salleTest->setNombrePorte(1);
        $salleTest->setNombreFenetre(1);
        $salleTest->setContientPc(true);
        $salleTest->setSystemeAcquisition($saTest);

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($saTest);
        $manager->persist($salleTest);
        $manager->flush();

        // simulate $testUser being logged in
        $client->loginUser($testUser);


        $crawler = $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form['form[commentaire]'] = 'Erreur de test';
        $form['form[emailDemandeur]'] = 'jmalki@exemple.com';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        $this->assertMatchesRegularExpression("/\/accueil\/$/", $client->getResponse()->headers->get('location'));

        $sa = $saRepository->findOneBy(['nom' => 'ESP-000']);
        $this->assertEquals("Réparation", $sa->getEtat());

        $demandeTrauxRepository = static ::getContainer()->get(DemandeTravauxRepository::class);
        $demande = $demandeTrauxRepository->findOneBy(['type' => 'Reparation', 'salle' => $salleTest->getId()]);
        $this->assertNotNull($demande);
        $this->assertEquals("Réparation", $demande->getType());
        $this->assertEquals("Erreur de test", $demande->getCommentaire());
        $this->assertEquals("ESP-000", $demande->getSystemeAcquisition()->getNom());
        $this->assertFalse($demande->isTerminee());

        $sql = "DELETE FROM demande_travaux WHERE id = " . $demande->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();

        $sql = "DELETE FROM salle WHERE id = " . $salleTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();

        $sql = "DELETE FROM systeme_acquisition WHERE id = " . $saTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();
    }

    public function test_signaler_erreur_requette_avec_utilisateur_connecte_en_temps_que_technicien_email_non_valide(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $saTest = new SystemeAcquisition();
        $saTest->setNom("ESP-000");
        $saTest->setEtat("Opérationnel");
        $saTest->setBaseDonnees("sae34bdk1eq1");


        $salleTest = new Salle();
        $salleTest->setNom("X000");
        $salleTest->setBatiment("X");
        $salleTest->setNombrePorte(1);
        $salleTest->setOrientation("NE");
        $salleTest->setNombreFenetre(1);
        $salleTest->setContientPc(true);
        $salleTest->setSystemeAcquisition($saTest);

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($saTest);
        $manager->persist($salleTest);
        $manager->flush();

        $crawler = $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form['form[commentaire]'] = 'Erreur de test';

        $client->submit($form);
        // code de reponse 422
        $this->assertResponseStatusCodeSame(422, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form['form[commentaire]'] = 'Erreur de test';

        $client->submit($form);

        $form['form[emailDemandeur]'] = 'brhjuvboizubvdn';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(422, $client->getResponse()->getStatusCode());

        $sql = "DELETE FROM salle WHERE id = " . $salleTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();

        $sql = "DELETE FROM systeme_acquisition WHERE id = " . $saTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();
    }

    public function test_signaler_erreur_requette_avec_utilisateur_connecte_en_temps_que_technicien_description_vide(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $saTest = new SystemeAcquisition();
        $saTest->setNom("ESP-000");
        $saTest->setEtat("Opérationnel");
        $saTest->setBaseDonnees("sae34bdk1eq1");

        $salleTest = new Salle();
        $salleTest->setNom("X000");
        $salleTest->setBatiment("X");
        $salleTest->setOrientation("NE");
        $salleTest->setNombrePorte(1);
        $salleTest->setNombreFenetre(1);
        $salleTest->setContientPc(true);
        $salleTest->setSystemeAcquisition($saTest);

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($saTest);
        $manager->persist($salleTest);
        $manager->flush();

        $crawler = $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form['form[emailDemandeur]'] = 'jmalki@exemple.com';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(422, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('submit')->form();
        $form['form[commentaire]'] = 'Erreur de test';

        $client->submit($form);
        $form['form[commentaire]'] = '';
        $client->submit($form);

        $this->assertResponseStatusCodeSame(422, $client->getResponse()->getStatusCode());

        $sql = "DELETE FROM salle WHERE id = " . $salleTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();

        $sql = "DELETE FROM systeme_acquisition WHERE id = " . $saTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();

    }

    public function test_signaler_erreur_requette_avec_utilisateur_connecte_en_temps_que_technicien_salle_invalide(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $client->request('GET', '/signaler-erreur/9999');

        //redirect to accueil
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());

        $this->assertResponseRedirects('/accueil/', 302);
    }

    public function test_signaler_erreur_requette_avec_utilisateur_connecte_en_temps_que_technicien_salle_sans_sa():void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $salleTest = new Salle();
        $salleTest->setNom("X000");
        $salleTest->setBatiment("X");
        $salleTest->setOrientation("NE");
        $salleTest->setNombrePorte(1);
        $salleTest->setNombreFenetre(1);
        $salleTest->setContientPc(true);

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($salleTest);
        $manager->flush();

        $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

        $this->assertResponseRedirects('/accueil/', 302);

        $sql = "DELETE FROM salle WHERE id = " . $salleTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();
    }

    public function test_signaler_erreur_requette_avec_utilisateur_connecte_en_temps_que_technicien_salle_avec_non_operationnel():void
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        $client->loginUser($testUser);

        $saTest = new SystemeAcquisition();
        $saTest->setNom("ESP-000");
        $saTest->setEtat("Non installé");
        $saTest->setBaseDonnees("sae34bdk1eq1");

        $salleTest = new Salle();
        $salleTest->setNom("X000");
        $salleTest->setBatiment("X");
        $salleTest->setOrientation("NE");
        $salleTest->setNombrePorte(1);
        $salleTest->setNombreFenetre(1);
        $salleTest->setContientPc(true);
        $salleTest->setSystemeAcquisition($saTest);

        $manager = static::getContainer()->get('doctrine')->getManager();
        $manager->persist($saTest);
        $manager->persist($salleTest);
        $manager->flush();

        $etats = ["Non installé", "Installation", "Réparation"];

        foreach ($etats as $etat) {
            $saTest->setEtat($etat);
            $manager->flush();

            $client->request('GET', '/signaler-erreur/' . $salleTest->getId());

            $this->assertResponseRedirects('/accueil/', 302);
        }

        $sql = "DELETE FROM salle WHERE id = " . $salleTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();

        $sql = "DELETE FROM systeme_acquisition WHERE id = " . $saTest->getId();
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->executeQuery();
    }




}

