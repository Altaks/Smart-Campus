<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;

use App\Entity\Utilisateur;

class UtilisateurTest extends TestCase
{
    public function test_un_utilisateur_a_un_identifiant(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setIdentifiant("admin");
        $this->assertEquals($utilisateur->getIdentifiant(), "admin");
    }

    public function test_un_utilisateur_a_un_mot_de_passe(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setMotDePasse("password");
        $this->assertEquals($utilisateur->getMotDePasse(), "password");
    }

    public function test_un_utilisateur_a_une_liste_de_role(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->addRole("ROLE_ADMIN");
        $utilisateur->addRole("ROLE_USER");
        $roles = new ArrayCollection();
        $roles->add("ROLE_ADMIN");
        $roles->add("ROLE_USER");
        $this->assertEquals($utilisateur->getRoles(), $roles);
    }
}
