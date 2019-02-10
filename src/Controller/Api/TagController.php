<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends AbstractFOSRestController implements ClassResourceInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/tags")
     */
    public function listTags()
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();
        $formatted = [];
        foreach ($tags as $tag) {
            $formatted[] = [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
                'slug' => $tag->getSlug(),
                'created at' => $tag->getCreatedAt(),
            ];
        }
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/tags/{id}")
     *
     * @param $id
     *
     * @return JsonResponse|View
     */
    public function show($id)
    {
        $tag = $this->em->getRepository(Tag::class)->find($id);
        if (empty($tag)) {
            return new JsonResponse(['message' => 'Tag not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            'slug' => $tag->getSlug(),
            'created at' => $tag->getCreatedAt(),
        ];
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/tag")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\Form\FormInterface|Tag
     */
    public function new(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($tag);
            $this->em->flush();

            return $tag;
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/tag/{id}")
     *
     * @param Request $request
     * @param Tag     $tag
     *
     * @return \Symfony\Component\Form\FormInterface|Tag
     */
    public function update(Request $request, Tag $tag)
    {
        $form = $this->createForm(TagType::class, $tag, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($tag, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/tag/{id}")
     *
     * @param Request $request
     * @param Tag     $tag
     *
     * @return \Symfony\Component\Form\FormInterface|Tag
     */
    public function remove(Request $request, Tag $tag)
    {
        $form = $this->createForm(TagType::class, $tag, [
            'method' => 'delete',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($tag, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }
}
