<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractFOSRestController implements ClassResourceInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/categories")
     */
    public function listCategories()
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        $formatted = [];
        foreach ($categories as $category) {
            $formatted[] = [
                'id' => $category->getId(),
                'title' => $category->getTitle(),
                'slug' => $category->getSlug(),
                'created at' => $category->getCreatedAt(),
            ];
        }
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/categories/{id}")
     *
     * @param $id
     *
     * @return JsonResponse|View
     */
    public function show($id)
    {
        $category = $this->em->getRepository(Category::class)->find($id);
        if (empty($category)) {
            return new JsonResponse(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [
            'id' => $category->getId(),
            'title' => $category->getTitle(),
            'slug' => $category->getSlug(),
            'created at' => $category->getCreatedAt(),
        ];
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/category")
     *
     * @param Request $request
     *
     * @return Category|\Symfony\Component\Form\FormInterface
     */
    public function new(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();

            return $category;
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/category/{id}")
     *
     * @param Request  $request
     * @param Category $category
     *
     * @return Category|\Symfony\Component\Form\FormInterface
     */
    public function update(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($category, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/category/{id}")
     *
     * @param Request  $request
     * @param Category $category
     *
     * @return Category|\Symfony\Component\Form\FormInterface
     */
    public function remove(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'delete',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($category, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }
}
