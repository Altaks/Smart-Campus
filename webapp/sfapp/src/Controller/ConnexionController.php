<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;


class ConnexionController extends AbstractController
{

    #[Route('/connexion', name: 'app_connexion')]
    public function index(AuthenticationUtils $authenticationUtils, Security $security, Request $request, ManagerRegistry $registry): Response
    {
        $erreur = $authenticationUtils->getLastAuthenticationError();

        $dernierIdentifiant = $authenticationUtils->getLastUsername();

        return $this->render('connexion/index.html.twig', [
            'dernier_identifiant' => $dernierIdentifiant,
            'erreur' => $erreur,
        ]);
    }

    #[Route('/auth-Success', name: 'auth-success')]
    public function onAuthenticationSuccess(Security $security, Request $request)
    {
        $utilisateur = $security->getUser();
        $security->login($utilisateur);

        if ($this->isGranted("ROLE_CHARGE_DE_MISSION")) {
            return $this->redirect('/accueil/charge-de-mission');
        } elseif ($this->isGranted("ROLE_TECHNICIEN")){
            return $this->redirect('/accueil/tech');
        }

    }
}
