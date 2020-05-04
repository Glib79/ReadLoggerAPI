<?php
declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\UserBookDataTransformer;
use App\DTO\BaseDto;
use App\DTO\UserBookDto;
use App\Repository\UserBookRepository;
use App\Service\UserBookManager;
use App\Support\Error\ValidationException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="user_book_api")
 */
class UserBookController extends BaseApiController
{
    /**
     * @var UserBookDataTransformer
     */
    private $userBookDataTransformer;

    /**
     * @var UserBookManager
     */
    private $userBookManager;
    
    /**
     * @var UserBookRepository
     */
    private $userBookRepository;

    /**
     * UserBookController constructor
     * @param UserBookDataTransformer $userBookDataTransformer
     * @param UserBookManager $userBookManager
     * @param UserBookRepository $userBookRepository
     */
    public function __construct(
        UserBookDataTransformer $userBookDataTransformer,
        UserBookManager $userBookManager, 
        UserBookRepository $userBookRepository
    )
    {
        $this->userBookDataTransformer = $userBookDataTransformer;
        $this->userBookManager = $userBookManager;
        $this->userBookRepository = $userBookRepository;
    }
    
    /**
     * Add book to user
     * @param Request $request
     * @return JsonResponse
     * @Route("/user-book", name="user_book_add", methods={"POST"})
     */
    public function addUserBook(Request $request): JsonResponse
    {
        try {      
            /** @var UserBookDto */
            $dto = $this->userBookDataTransformer->transformRequest($request);
            $user = $this->getUser();
            $dto->userId = $user->getId();
            
            $dto->validate([BaseDto::GROUP_CREATE]);
            
            $id = $this->userBookManager->createUserBook($dto);
            
            return $this->response(Response::HTTP_CREATED, 'Book created for User', ['id' => $id]);
        } catch (ValidationException $e) {
            return $this->response(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e->getErrors());
        }
    }
//    
//    /**
//     * Delete category
//     * @param string $id
//     * @return JsonResponse
//     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
//     */
//    public function deleteCategory(string $id): JsonResponse
//    {
//        $category = $this->categoryRepository->getCategoryById($id);
//        
//        if (!$category) {
//            return $this->response(Response::HTTP_NOT_FOUND, 'Category not found');
//        }
//        
//        $this->categoryManager->deleteCategory($id);
//            
//        return $this->response(Response::HTTP_OK, 'Category deleted');
//    }
    
    /**
     * Books List
     * @return JsonResponse
     * @Route("/user-books", name="user_books", methods={"GET"})
     */
    public function getUserBooks(): JsonResponse
    {
        $user = $this->getUser();
        $data = $this->userBookManager->findUsersBooks($user->getId()->toString());
        
        $outputList = $this->userBookDataTransformer->transformList($data, [BaseDto::GROUP_LIST]);
        
        return $this->response(
            Response::HTTP_OK, 
            'Books found', 
            $outputList, 
            ['count' => count($outputList)]
        );
    }

//    /**
//     * Get single category
//     * @param string $id
//     * @return JsonResponse
//     * @Route("/category/{id}", name="category_get", methods={"GET"})
//     */
//    public function getCategory(string $id): JsonResponse
//    {
//        $category = $this->categoryRepository->getCategoryById($id);
//
//        if (!$category) {
//            return $this->response(Response::HTTP_NOT_FOUND, 'Category not found');
//        }
//        
//        $output = $this->categoryDataTransformer->transformOutput($category, [BaseDto::GROUP_SINGLE]);
//
//        return $this->response(Response::HTTP_OK, 'Category found', $output);
//    }
//
//    /**
//     * Update category
//     * @param Request $request
//     * @param string $id
//     * @return JsonResponse
//     * @Route("/category/{id}", name="category_put", methods={"PUT"})
//     */
//    public function updateCategory(Request $request, string $id): JsonResponse
//    {
//        try {
//            $category = $this->categoryRepository->getCategoryById($id);
//
//            if (!$category) {
//                return $this->response(Response::HTTP_NOT_FOUND, 'Category not found');
//            }
//
//            /** @var CategoryDto */
//            $dto = $this->categoryDataTransformer->transformInput($request);
//            $dto->id = Uuid::fromString($category['id']);
//            
//            $dto->validate([BaseDto::GROUP_UPDATE]);
//            
//            $this->categoryManager->updateCategory($dto);
//
//            return $this->response(Response::HTTP_OK, 'Category updated');
//        } catch (ValidationException $e) {
//            return $this->response(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e->getErrors());
//        }
//    }
}

