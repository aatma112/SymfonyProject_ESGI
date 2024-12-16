<?php

namespace App\Controller\Backend;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categories', name: "admin.categories")]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(CategoryRepository $repo): Response
    {
        return $this->render('backend/category/index.html.twig', [
            'categories' => $repo->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'La catégorie a bien été créée.');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('backend/category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Category $category, Request $request): Response
    {
        if (!$category) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas.');

            return $this->redirectToRoute('admin.categories.index');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La catégorie a bien été modifiée.');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('backend/category/update.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Category $category, Request $request): Response
    {
        if (!$category) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas.');

            return $this->redirectToRoute('admin.categories.index');
        }

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('token'))) {
            $this->em->remove($category);
            $this->em->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide.');
        }

        return $this->redirectToRoute('admin.categories.index');
    }
}