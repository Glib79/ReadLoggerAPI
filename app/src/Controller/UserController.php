<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BaseDto;
use App\Service\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="user_api")
 */
class UserController extends BaseApiController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * UserController constructor
     * @param UserManager $userManager
     */
    public function __construct(
        UserManager $userManager
    )
    {
        $this->userManager = $userManager;
    }
    
    /**
     * Resend confirmation email (with new token)
     * @return JsonResponse
     * @Route("/user/resend-confirmation-email", name="user_resend_confirmation_email", methods={"GET"})
     */
    public function resendConfirmationEmail(): JsonResponse
    {
        $user = $this->getUser();
        
        $this->userManager->resendConfirmationEmail($user->getId());
        
        return $this->response(
            Response::HTTP_OK, 
            'Email resend', 
            ['status' => true]
        );
    }
}
