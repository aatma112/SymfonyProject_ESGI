<?php

namespace App\Controller\Backend;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/article', name: 'admin.article')]
class ArticleController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em,
    ){}

    #[Route('', '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('backend/article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

   #[Route('/create', '.create', methods: ['GET', 'POST'] )]
    public function create(Request $request): Response
    {
        $article = new Article();
        $article->setUser(
            user: $this->getUser()
        );

        $form = $this->createForm(ArticleType::class,$article );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();
            $this->addFlash('success', 'Article Updated successfully created');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('backend/article/create.html.twig', [
            'form' => $form,
        ]);
        
    }

    #[Route('/{id}/update', '.update', methods: ['GET', 'POST'] )]
    public function update(?Article $article, Request $request): Response
    {
        if(!$article){
            $this->addFlash('error', 'Article unavailable !');
            return $this->redirectToRoute('admin.article.index');
        }

        $form = $this->createForm(ArticleType::class, $article, ['isEdit' => true] );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', message: 'Article modified successfully !');

            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('backend/article/update.html.twig',  [
            'form' => $form,
    ]);
        
    }

    #[Route('/{id}/delete', '.delete',  methods: ['POST'])]
    //methode plus récente:
    public function delete(?Article $article, Request $request): RedirectResponse {
        if (!$article) {
            $this->addFlash('error', message: 'Article not found');
            return $this->redirectToRoute('admin.article.index');
        }

        if ($this->isCsrfTokenValid('delete' .$article->getId(), $request->request->get('token'))) {
            $this->em->remove(object: $article);
            $this->em->flush();

            $this->addFlash('success', 'Article deleted successfully');

        } else {
            $this->addFlash('error', 'Invalid CSRF token');

        }
        return $this->redirectToRoute('admin.article.index');
     
    }

}
