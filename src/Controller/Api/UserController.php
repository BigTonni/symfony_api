<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class UserController extends AbstractFOSRestController implements ClassResourceInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users")
     * @SWG\Response(
     *     response=200,
     *     description="Returns users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     */
    public function listUsers()
    {
        $users = $this->em->getRepository(User::class)->findAll();
        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->getId(),
                'full name' => $user->getFullName(),
                'user name' => $user->getUserName(),
                'email' => $user->getEmail(),
                'created at' => $user->getCreatedAt(),
            ];
        }
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns a user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     *
     * @param $id
     *
     * @return JsonResponse|View
     */
    public function show($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [
            'id' => $user->getId(),
            'full name' => $user->getFullName(),
            'user name' => $user->getUserName(),
            'email' => $user->getEmail(),
            'created at' => $user->getCreatedAt(),
        ];
        $view = View::create($formatted);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/user")
     * @SWG\Response(
     *     response=200,
     *     description="Create a new user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\Form\FormInterface|User
     */
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/user/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Update the user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     *
     * @param Request $request
     * @param User    $user
     *
     * @return \Symfony\Component\Form\FormInterface|User
     */
    public function update(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($user, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/user/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Users")
     *
     * @param Request $request
     * @param User    $user
     *
     * @return \Symfony\Component\Form\FormInterface|User
     */
    public function remove(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'delete',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return View::create($user, Codes::HTTP_NO_CONTENT);
        }

        return $form;
    }
}
