<?php
declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\UserDataTransformer;
use App\DTO\BaseDto;
use App\DTO\UserDto;
use App\Repository\UserRepository;
use App\Service\UserManager;
use App\Support\Error\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends BaseApiController
{
    /**
     * @var UserDataTransformer
     */
    private $userDataTransformer;
    
    /**
     * @var UserManager
     */
    private $userManager;
   
    /**
     * @var UserRepository
     */
    private $userRepository;
    
    /**
     * AuthController constructor
     * @param UserDataTransformer $userDataTransformer
     * @param UserManager $userManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserDataTransformer $userDataTransformer, 
        UserManager $userManager,
        UserRepository $userRepository
    )
    {
        $this->userDataTransformer = $userDataTransformer;
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
    }
    
    /**
     * Login user
     * @param UserInterface $user
     * @return JsonResponse
     * @Route("/auth/login_check", name="login_check", methods={"POST"})
     */
    public function getTokenUser(UserInterface $user): JsonResponse
    {
        $data = $this->userRepository->getUserById($user->getId()->toString());
        
        return $this->response(
            Response::HTTP_OK,
            'User authenticated',
            [
                'token' => $this->userManager->generateJWTToken($user),
                'user'  => $this->userDataTransformer->transformOutput($data, [BaseDto::GROUP_SINGLE])
            ]
        );
    }
    
    /**
     * Register endpoint
     * @param Request $request
     * @return JsonResponse
     * @Route("/auth/register", name="register", methods={"POST"})
     */
    public function register(Request $request): JsonResponse
    {
        try {
            /** @var UserDto */
            $dto = $this->userDataTransformer->transformRequest($request);
            
            $dto->validate([BaseDto::GROUP_CREATE]);
            
            $id = $this->userManager->createUser($dto);
            
            return $this->response(
                Response::HTTP_CREATED,
                sprintf('User %s successfully created', $dto->email), 
                ['id' => $id]
            );
        } catch (ValidationException $e) {
            return $this->response(Response::HTTP_BAD_REQUEST, $e->getMessage(), $e->getErrors());
        }
    }
}