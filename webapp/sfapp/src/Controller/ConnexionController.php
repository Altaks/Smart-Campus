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
        $formData = [
            'test' => 'test',
            'test2' => 'test2',
        ];

        $model = new TestObject();
        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(TestedType::class, $model);

        $expected = new TestObject();
        // ...populate $expected properties with the data stored in $formData

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $model was modified as expected when the form was submitted
        $this->assertEquals($expected, $model);
    }

}
