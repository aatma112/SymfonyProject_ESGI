<?php

namespace App\Controller\Backend;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users', name: 'admin.users')]
class UserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em){

    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backend/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: '.update', methods: ['GET', 'POST'])]

    //methode plus récente:
     public function update(?User $user, Request $request): Response {
       if(!$user){
        $this->addFlash('error','User not found');
        return $this->redirectToRoute('admin.users.index');
       }

       $form = $this->createForm(UserType::class, $user, ['isAdmin'=>true]);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid()){
        $this->em->persist($user);
        $this->em->flush();
        $this->addFlash('success', 'User updated successfully');

        return $this->redirectToRoute('admin.users.index');

       }
       return $this->render('backend/user/update.html.twig', [
        'form' => $form
        ]);
    }

    
    #[Route('/{id}/delete', name: '.delete', methods: [ 'POST'])]
    //methode plus récente:
     public function delete(?User $user, Request $request): RedirectResponse {
        if (!$user) {
            $this->addFlash('error', message: 'User not found');
            return $this->redirectToRoute('admin.users.index');
        }

        if ($this->isCsrfTokenValid('delete' .$user->getId(), $request->request->get('token'))) {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', 'User deleted successfully');

        } else {
            $this->addFlash('error', 'Invalid token');

        }
        return $this->redirectToRoute('admin.users.index');
     
    }

    


}