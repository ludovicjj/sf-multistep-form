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
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllOrderByPosition();

        return $this->render('article/index.html.twig', [
            'articles' => $articles
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
}
