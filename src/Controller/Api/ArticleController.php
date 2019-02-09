<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractFOSRestController implements ClassResourceInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/articles")
     */
    public function getArticles()
    {
        $articles = $this->em->getRepository(Article::class)->findAll();
        $formatted = [];
        foreach ($articles as $article) {
            $formatted[] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'body' => $article->getBody(),
                'category' => $article->getCategory()->getTitle(),
                'author' => $article->getAuthor()->getFullName(),
                'created at' => $article->getCreatedAt(),
            ];
        }
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/articles/{id}")
     *
     * @param $id
     *
     * @return JsonResponse|View
     */
    public function show($id)
    {
        $article = $this->em->getRepository(Article::class)->find($id);
        if (empty($article)) {
            return new JsonResponse(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'category' => $article->getCategory()->getTitle(),
            'author' => $article->getAuthor()->getFullName(),
            'created at' => $article->getCreatedAt(),
        ];
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/article")
     *
     * @param Request $request
     *
     * @return Article|\Symfony\Component\Form\FormInterface
     */
    public function new(Request $request)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            return $article;
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/article/{id}")
     *
     * @param Request $request
     * @param Article $article
     *
     * @return Article|\Symfony\Component\Form\FormInterface
     */
    public function update(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($article, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }
}
