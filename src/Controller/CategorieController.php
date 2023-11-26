<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    #[Route('/categories/add', name: 'add_categories', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $description = $data['description'];
        $categories_exist = $this->categoriesRepository->findOneBy(['name' => $name, 'deleted' => false]);
        if (!$categories_exist) {
            if (empty($description) || empty($name)) {
                // throw new BadRequestException('Expecting mandatory parameters!');
                return new JsonResponse(
                    [
                        'data' => null,
                        'message' => 'Expecting mandatory parameters!',
                        'statusCode' => 400,
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $data = $this->categoriesRepository->
                saveCategories($name, $description, false, new \DateTimeImmutable());

            $data = [
                'id' => $data->getId(),
                'name' => $data->getName(),
                'description' => $data->getDescription(),
                'deleted' => $data->getDeleted(),
                'createdAt' => $data->getCreatedAt(),
            ];
            return new JsonResponse([
                'data' => $data,
                'message' => 'categories created!',
                'statusCode' => 201
            ], Response::HTTP_CREATED);
        } else {
            return new JsonResponse(
                [
                    'data' => null,
                    'message' => 'Categories already exist!',
                    'statusCode' => 409,
                ],
                Response::HTTP_CONFLICT
            );
        }
    }

    #[Route('/categories/{id}', name: 'get_one_categories', methods: ['GET'])]
    public function get($id): JsonResponse
    {
        $categories = $this->categoriesRepository->findOneBy(['id' => $id, 'deleted' => false]);
        $data = [
            'id' => $categories->getId(),
            'name' => $categories->getName(),
            'description' => $categories->getDescription(),
            'deleted' => $categories->getDeleted(),
            'createdAt' => $categories->getCreatedAt(),
        ];
        return new JsonResponse(
            [
                'data' => $data,
                'message' => 'Operation success',
                'statusCode' => 200,
            ],
            Response::HTTP_OK
        );
    }


    #[Route('/categories/{id}', name: 'update_categories', methods: ['PUT'])]
    public function update($id, Request $request): JsonResponse
    {
        $categories = $this->categoriesRepository->findOneBy(['id' => $id, 'deleted' => false]);
        if ($categories) {
            $data = json_decode($request->getContent(), true);

            empty($data['name']) ? true : $categories->setName($data['name']);
            empty($data['description']) ? true : $categories->setDescription($data['description']);
            empty($data['deleted']) ? true : $categories->setDeleted($data['deleted']);
            empty($data['createdAt']) ? true : $categories->setCreatedAt($data['createdAt']);

            $updatedCategories = $this->categoriesRepository->updateCategories($categories);
            $data = [
                'id' => $updatedCategories->getId(),
                'name' => $updatedCategories->getName(),
                'description' => $updatedCategories->getDescription(),
                'deleted' => $updatedCategories->getDeleted(),
                'createdAt' => $updatedCategories->getCreatedAt(),
            ];
            return new JsonResponse(
                [
                    'data' => $data,
                    'message' => 'Operation reussit',
                    'statusCode' => 200,
                ],
                Response::HTTP_OK
            );
        } else {
            return new JsonResponse(
                [
                    'data' => null,
                    'message' => 'Categories not found!',
                    'statusCode' => 404,
                ],
                Response::HTTP_NOT_FOUND
            );
        }
    }


    #[Route('/categories/{id}', name: 'delete_categories', methods: ['DELETE'])]
    public function delete($id): JsonResponse
    {
        $categories = $this->categoriesRepository->findOneBy(['id' => $id, 'deleted' => false]);
        if ($categories) {
            $this->categoriesRepository->removeCategories($categories);

            return new JsonResponse(
                [
                    'data' => null,
                    'message' => 'Categories deleted',
                    'statusCode' => 200,
                ],
                Response::HTTP_OK
            );
        } else {
            return new JsonResponse(
                [
                    'data' => null,
                    'message' => 'Categories not found!',
                    'statusCode' => 404,
                ],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    #[Route('/categories', name: 'app_categories', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoriesRepository->findBy(['deleted' => false]);
        $data = array();
        foreach ($categories as $category) {
            array_push($data, [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'deleted' => $category->getDeleted(),
                'createdAt' => $category->getCreatedAt(),
            ]);
        }
        return new JsonResponse(
            [
                'data' => $data,
                'message' => 'Categories deleted',
                'statusCode' => 200,
            ],
            Response::HTTP_OK
        );
    }
}
