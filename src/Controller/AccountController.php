<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{

    /**
     * Permet de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name:"account_login")]
    public function login(AuthenticationUtils $utils): Response 
    {

        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        $loginError = null;

        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            $loginError = "Trop de tentatives de connexion, veuillez réessayer plus tard";
        }

        return $this->render('account/index.html.twig',[
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError,
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @return void
     */
    #[Route('/logout', name:"account_logout")]
    public function logout(): void
    {

    }

    /**
     * Permet de s'enregistrer
     *
     * @param Request $resquest
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/register', name:"account_register")]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute("account_login");
        }

        

        return $this->render('account/registration.html.twig',[
            'myForm' => $form->createView()
        ]);
    }   
}
