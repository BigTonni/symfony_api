<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
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
     * @SWG\Response(
     *     response=200,
     *     description="Returns categories",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Categories")
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
     * @SWG\Response(
     *     response=200,
     *     description="Returns a category",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Categories")
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
     * @Rest\Post("/categories")
     * @SWG\Response(
     *     response=200,
     *     description="Create a new category",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Categories")
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
     * @Rest\Put("/categories/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Update the category",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Categories")
     *
     * @param Request  $request
     * @param Category $category
     *
     * @return JsonResponse|\Symfony\Component\Form\FormInterface
     */
    public function update(Request $request, Category $category)
    {
        $category = $this->em->getRepository(Category::class)->find($category->getId());
        if (!$category) {
            return new JsonResponse(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }
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
     * @Rest\Delete("/categories/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the category",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Category::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Categories")
     *
     * @param Category $category
     *
     * @return JsonResponse
     */
    public function remove(Category $category)
    {
        $category = $this->em->getRepository(Category::class)->find($category->getId());
        if ($category) {
            $this->em->remove($category);
            $this->em->flush();

            return new JsonResponse(null, 204);
        }

        return new JsonResponse(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }
}
