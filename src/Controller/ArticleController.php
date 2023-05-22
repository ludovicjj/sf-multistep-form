<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name:'app_article')]
    public function index(
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $articles = $articleRepository->findAllOrderByPosition();

        $entityManager->getFilters()->disable('softdeleteable');
        $deletedArticles = $articleRepository->findAllDeleted();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'deletedArticles' => $deletedArticles
        ]);
    }

    #[Route('/article/reorder', name:'app_article_reorder', methods: ['POST'])]
    public function reorderArticle(
        Request $request,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    )
    {
        $orderedIds = json_decode($request->getContent(), true);

        if ($orderedIds === null) {
            return $this->json(['mesage' => 'Invalid body'], 400);
        }
        $orderedIds = array_flip($orderedIds);

        foreach ($articleRepository->findAll() as $article) {
            $article->setPosition($orderedIds[$article->getId()]);
        }


        $entityManager->flush();
        return $this->json(['mesage' => 'success'], 200);
    }

    #[Route('/article/delete/{id}', name:'app_article_delete', methods: ['GET'])]
    public function delete(
        Article $article,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_article');
    }

    #[Route('/article/undelete/{id}', name:'app_article_undelete', methods: ['GET'])]
    public function undelete(
        int $id,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->getFilters()->disable('softdeleteable');
        $article = $articleRepository->find($id);
        $article->setDeletedAt(null);
        $entityManager->flush();

        return $this->redirectToRoute('app_article');
    }
}
