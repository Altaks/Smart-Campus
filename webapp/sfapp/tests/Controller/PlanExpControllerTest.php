<?php

namespace App\tests\Controller;

use App\Entity\SystemeAcquisition;
use App\Repository\DemandeTravauxRepository;
use App\Repository\SystemeAcquisitionRepository;
use App\Entity\Salle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UtilisateurRepository;


class PlanExpControllerTest extends WebTestCase
{
    public function test_cdm_demander_installation_sur_salle_valide(): void
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        $client->loginUser($utilisateur);

        // Vérifier si la salle D001 existe
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $sallesRepository = $entityManager->getRepository(Salle::class);

        $salleD = $sallesRepository->findOneBy(['nom' => 'D001']);

        if($salleD == null){

            $salle = new Salle();
            $salle->setNom("D001");
            $salle->setBatiment("Bâtiment D");
            $salle->setOrientation("No");
            $salle->setNombrePorte(1);
            $salle->setNombreFenetre(6);
            $salle->setSystemeAcquisition(null);
            $salle->setContientPc(false);

            $entityManager->persist($salle);
            $entityManager->flush();
        }

        $salleD = $sallesRepository->findOneBy(['nom' => 'D001']);

        $demande_travaux = $client->getContainer()->get('doctrine')->getRepository('App\Entity\DemandeTravaux')->findAll(['salle' => $salleD->getId()]);
        if($demande_travaux != null || $demande_travaux != []){
            foreach($demande_travaux as $dt){
                $entityManager->remove($dt);
            }
            $entityManager->flush();
        }

        $client->request('GET', '/plan/' . $salleD->getId() . '/demander-installation');
        $this->assertResponseRedirects("/plan");
        $client->followRedirect();

        $demande_travaux = $client->getContainer()->get('doctrine')->getRepository('App\Entity\DemandeTravaux')->findOneBy(['salle' => $salleD->getId()]);
        $this->assertNotNull($demande_travaux);
    }

    public function test_cdm_demander_installation_sur_salle_invalide(): void
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        $client->loginUser($utilisateur);

        // La salle d'id -1 n'existe pas, le serveur doit renvoyer une erreur 404
        $client->request('GET', '/plan/-1/demander-installation');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_cdm_demander_installation_sur_salle_avec_sa() : void
    {
        $client = static::createClient();

        $utilisateur = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Utilisateur')->findOneBy(['identifiant' => 'yghamri']);
        $this->assertNotNull($utilisateur);

        $client->loginUser($utilisateur);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $systemeAcquisition = $client->getContainer()->get('doctrine')->getRepository('App\Entity\SystemeAcquisition')->findOneBy(['nom' => 'ESP-123']);

        if($systemeAcquisition == null) {
            $systemeAcquisition = new SystemeAcquisition();
            $systemeAcquisition->setNom("ESP-123");
            $systemeAcquisition->setBaseDonnees("sae34bdm2eq3");
            $systemeAcquisition->setEtat("Opérationnel");

            $entityManager->persist($systemeAcquisition);
            $entityManager->flush();
        }

        $salle = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Salle')->findOneBy(['nom' => 'X001']);

        if($salle == null){
            $salle = new Salle();
            $salle->setNom("X001");
            $salle->setBatiment("Bâtiment X");
            $salle->setOrientation("No");
            $salle->setNombrePorte(1);
            $salle->setNombreFenetre(6);
            $salle->setSystemeAcquisition($systemeAcquisition);
            $salle->setContientPc(false);
            $entityManager->persist($salle);
            $entityManager->flush();
        }

        $sa = $entityManager->getRepository(SystemeAcquisition::class)->findOneBy(['nom' => 'ESP-123']);
        $this->assertNotNull($sa);
        $salle = $entityManager->getRepository(Salle::class)->findOneBy(['nom' => 'X001']);
        $this->assertNotNull($salle);

        // La salle d'id -1 n'existe pas, le serveur doit renvoyer une erreur 404
        $client->request('GET', '/plan/' . $salle->getId() . '/demander-installation');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_cdm_plan_route_connexion_valide(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan');
        $this->assertResponseIsSuccessful();
    }

    public function test_cdm_plan_route_connexion_invalide_technicien(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan');
        $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_cdm_plan_route_connexion_invalide_usager(): void
    {
        $client = static::createClient();

        $client->request('GET', '/plan');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_demande_travaux_route_connexion_valide(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $travauxRepository = static ::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findAll();

        for($i = 0; $i < count($travaux); $i++) {
            $client->request('GET', '/plan/demande-travaux/'.$travaux[$i]->getId());
            $this->assertResponseIsSuccessful();
        }
    }

    public function test_demande_travaux_route_connexion_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $travauxRepository = static ::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findAll();

        for($i = 0; $i < count($travaux); $i++) {
            $client->request('GET', '/plan/demande-travaux/'.$travaux[$i]->getId());
            $this->assertResponseStatusCodeSame(403, $client->getResponse()->getStatusCode());
        }
    }

    public function test_demande_travaux_route_connexion_invalide_usager(): void
    {
        $client = static::createClient();

        $travauxRepository = static ::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findAll();

        for($i = 0; $i < count($travaux); $i++) {
            $client->request('GET', '/plan/demande-travaux/'.$travaux[$i]->getId());
            $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
        }
    }

    public function test_demande_travaux_nombre_salle_liste_correspond_nombre_salle_bd(){

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $systemesAcquisitionRepository = static ::getContainer()->get(SystemeAcquisitionRepository::class);
        $nbSystemesAcquisitionNonInstalle = count($systemesAcquisitionRepository->findBy(['etat' => 'Non installé']));

        $travauxRepository = static ::getContainer()->get(DemandeTravauxRepository::class);
        $travaux = $travauxRepository->findAll();

        for($i = 0; $i < count($travaux); $i++) {
            $crawler = $client->request('GET', '/plan/demande-travaux/'.$travaux[$i]->getId());

            $option = $crawler->filter("#option")->filter('option');

            if( $travaux[$i]->getSystemeAcquisition() == null){
                $this->assertEquals($nbSystemesAcquisitionNonInstalle+1, $option->count());
            }
            else{
                $this->assertEquals($nbSystemesAcquisitionNonInstalle+2, $option->count());
            }


        }
    }

    public function test_technicien_lister_SA_route_connexion_valide(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'jmalki']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister_sa/');
        $this->assertResponseIsSuccessful();

    }

    public function test_lister_SA_route_connexion_invalide_usager(): void
    {
        $client = static::createClient();
        $client->request('GET', '/plan/lister_sa/');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }

    public function test_lister_SA_route_connexion_invalide_charge_de_mission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);
        $testUser = $userRepository->findOneBy(['identifiant' => 'yghamri']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/plan/lister_sa/');
        $this->assertResponseStatusCodeSame(302, $client->getResponse()->getStatusCode());
    }
}