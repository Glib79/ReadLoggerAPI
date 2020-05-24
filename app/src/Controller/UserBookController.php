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
    private const DEFAULT_LIMIT = 10;
    
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
    
    /**
     * Delete user book
     * @param string $id
     * @return JsonResponse
     * @Route("/user-book/{id}", name="user_book_delete", methods={"DELETE"})
     */
    public function deleteUserBook(string $id): JsonResponse
    {
        $user = $this->getUser();
        
        $this->userBookManager->deleteUserBook($id, $user->getId()->toString());
            
        return $this->response(Response::HTTP_OK, 'User book deleted');
    }

    /**
     * Get single user book
     * @param string $id
     * @return JsonResponse
     * @Route("/user-book/{id}", name="user_book_get", methods={"GET"})
     */
    public function getUserBook(string $id): JsonResponse
    {
        $data = $this->userBookManager->getUsersBookById($id);

        if (!$data) {
            return $this->response(Response::HTTP_NOT_FOUND, 'Book not found');
        }
        
        $output = $this->userBookDataTransformer->transformOutput($data, [BaseDto::GROUP_SINGLE]);

        return $this->response(Response::HTTP_OK, 'Users book found', $output);
    }

    /**
     * Users books list
     * @return JsonResponse
     * @Route("/user-books", name="user_books", methods={"GET"})
     */
    public function getUserBooks(Request $request): JsonResponse
    {
        $limit = $this::DEFAULT_LIMIT;
        $page = $request->query->getInt('page', 1);
        $status = $request->query->getInt('status');
        $user = $this->getUser();
        $userId = $user->getId()->toString();
        
        $data = $this->userBookManager->findUsersBooks(
            $userId, 
            [
                'limit'  => $limit,
                'page'   => $page,
                'status' => $status
            ]
        );
        
        $outputList = $this->userBookDataTransformer->transformList($data, [BaseDto::GROUP_LIST]);
        
        return $this->response(
            Response::HTTP_OK, 
            'Books found', 
            $outputList, 
            [
                'count' => count($outputList),
                'page'  => $page,
                'pages' => ceil($this->userBookRepository->countUsersBooks($userId) / $limit)
            ]
        );
    }

    /**
     * Update user book
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @Route("/user-book/{id}", name="user_book_patch", methods={"PATCH"})
     */
    public function updateUserBook(Request $request, string $id): JsonResponse
    {
        $data = $this->userBookManager->getUsersBookById($id);

        if (!$data) {
            return $this->response(Response::HTTP_NOT_FOUND, 'Book not found');
        }
        
        $oldUserBook = $this->userBookDataTransformer->transformOutput($data, [BaseDto::GROUP_LOG]);
        
        try {
            $user = $this->getUser();
            /** @var UserBookDto */
            $dto = $this->userBookDataTransformer->transformRequest($request);
            $dto->id = Uuid::fromString($id);
            $dto->userId = $user->getId();
            
            $dto->validate([BaseDto::GROUP_UPDATE]);
            
            $this->userBookManager->updateUserBook($dto, $oldUserBook);

            return $this->response(Response::HTTP_OK, 'User book updated');
        } catch (ValidationException $e) {
            return $this->response(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e->getErrors());
        }
    }
}

