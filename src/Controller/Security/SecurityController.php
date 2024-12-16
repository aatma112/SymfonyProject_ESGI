<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app.security.login', methods:['GET', 'POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'lastUsername'=> $authUtils->getLastUsername(),
        ]);
    }
    #[Route(path: '/register', name: 'app.security.register', methods: ['GET','POST'])]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        // On instancie l'object qu'on  souhaite enregistrer
        $user = new User();

        // we create the form
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        // Verify if the foem is submitted
        if ($form->isSubmitted() && $form->isValid()) {

            // On hash le mdp (only for user)
            $user
                ->setPassword(
                    $hasher->hashPassword($user, $form->get('password')->getData())
                );
            // On persist et flush
            $em->persist($user);
            $em->flush();

            // On defini un message flash et on redirige
            $this->addFlash('success', 'Votre compte a bien ete cree!');

            return $this->redirectToRoute('app.security.login');

        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);

    }

}
