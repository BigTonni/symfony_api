<?php

namespace App\Controller\Web;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/{id}", methods={"GET"}, name="article_show")
     * @param Article $article
     * @ParamConverter("article", class="App:Article")
     */
    public function show(Article $article)
    {
        $article = $this->getDoctrine()->getManager()->getRepository(Article::class)->find($article->getId());
        $str_tags = '';
        if (false !== $arr_tags = $article->getTags()->getValues()) {
            $arr_tag_names = [];
            foreach ($arr_tags as $tag) {
                $arr_tags[] = $tag->getName();
            }
            $str_tags = implode(',', $arr_tags);
        }
        dump($str_tags);
        die;

        return;
//        $tags = $this->em->getRepository(Tag::class)->find($id);
        $formatted = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'category' => $article->getCategory()->getTitle(),
            'author' => $article->getAuthor()->getFullName(),
            'created at' => $article->getCreatedAt(),
        ];
        $view = View::create($formatted, Response::HTTP_OK);
        $view->setFormat('json');

        return $view;
    }
}
