<?php

namespace App\tests\Controller;

use App\Entity\SystemeAcquisition;
use App\Repository\DemandeTravauxRepository;
use App\Repository\SystemeAcquisitionRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UtilisateurRepository;


class PlanExpControllerTest extends WebTestCase
{
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
}