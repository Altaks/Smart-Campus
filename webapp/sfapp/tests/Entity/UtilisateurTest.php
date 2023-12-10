<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Utilisateur;
use App\Tests\Entity\ArrayCollection;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

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
        $this->assertNotEquals($utilisateur->getMotDePasse(), "password");
    }

    public function test_un_utilisateur_a_une_liste_de_role(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->addRole("ROLE_ADMIN");
        $utilisateur->addRole("ROLE_USER");
        $roles = array();
        $roles[] = "ROLE_ADMIN";
        $roles[] = "ROLE_USER";
        $this->assertEquals($utilisateur->getRoles(), $roles);
    }
}
