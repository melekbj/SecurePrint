<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{

    #[Route('/register', name: 'app_registration')]
    public function registration(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHashed): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHashed->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $request->getSession()->getFlashBag()->add('success', 'Account created successfully.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }


    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/accueil.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function errorM(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }




}
