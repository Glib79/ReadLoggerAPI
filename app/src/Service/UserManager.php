<?php
declare(strict_types=1);

namespace App\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\LogDto;
use App\DTO\UserDto;
use App\Repository\UserRepository;
use App\Service\LogManager;
use App\Support\User;
use App\Support\SendEmail;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Ramsey\Uuid\Uuid;

class UserManager
{
    public const ROLE_USER = 'ROLE_USER';
    
    private const TOKEN_LENGTH = 32;
    private const USER_TABLE = 'user';
    
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTManager;
    
    /**
     * @var LogDataTransformer
     */
    private $logDataTransformer;
 
    /**
     * @var LogManager
     */
    private $logManager;
    
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SendEmail
     */
    private $sendEmail;
    
    /**
     * UserManager constructor
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTTokenManagerInterface $JWTManager
     * @param LogDataTransformer $logDataTransformer
     * @param LogManager $logManager
     * @param UserRepository $userRepository
     * @param SendEmail $sendEmail
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        JWTTokenManagerInterface $JWTManager, 
        LogDataTransformer $logDataTransformer,
        LogManager $logManager,
        UserRepository $userRepository,
        SendEmail $sendEmail
    )
    {
        $this->encoder = $encoder;
        $this->JWTManager = $JWTManager;
        $this->logDataTransformer = $logDataTransformer;
        $this->logManager = $logManager;
        $this->userRepository = $userRepository;
        $this->sendEmail = $sendEmail;
    }
    
    /**
     * Generate JWT token based on User
     * @param UserInterface $user
     * @return string JWT token
     */
    public function generateJWTToken(UserInterface $user): string
    {
        return $this->JWTManager->create($user);
    }
    
    /**
     * Confirms email for user
     * @param string $id
     * @return void
     */
    public function confirmEmail(string $id): void
    {
        $this->userRepository->confirmEmail($id);

        $logDto = $this->logDataTransformer->prepareLog(
            Uuid::fromString($id), 
            LogDto::ACTION_CONFIRM_EMAIL, 
            self::USER_TABLE,
            null,
            [
                'id'          => $id,
                'isConfirmed' => true,
                'token'       => ''
            ]
        );
        
        $this->logManager->addLog($logDto);
    }
    
    /**
     * Create User from DTO
     * @param string $email
     * @param string $password
     * @return string $id - created record id
     */
    public function createUser(UserDto $dto): string
    {
        $dto->roles = [self::ROLE_USER];
        $dto->isActive = true;
        $dto->isConfirmed = false;
        $dto->token = $this->generateToken(self::TOKEN_LENGTH);
        
        $user = new User($dto->email);
        $user->setRoles($dto->roles);
        
        $dto->password = $this->encoder->encodePassword($user, $dto->password);
        
        $id = $this->userRepository->createUser($dto);
        $dto->id = Uuid::fromString($id);
        
        $this->sendEmail->sendEmail(
            ['to' => $dto->email], 
            [
                'template' => SendEmail::TEMPLATE_CONFIRM_EMAIL,
                'language' => $dto->language,
                'token'    => $dto->token
            ]
        );
        
        $logDto = $this->logDataTransformer->prepareLog(
            $dto->id, 
            LogDto::ACTION_CREATE, 
            self::USER_TABLE,
            $dto
        );
        
        $this->logManager->addLog($logDto);
        
        return $id;
    }
    
    /**
     * Creates new token and resend confirmation email
     * @param Uuid $id
     * @return void
     */
    public function resendConfirmationEmail(Uuid $id): void
    {
        $token = $this->generateToken(self::TOKEN_LENGTH);
        $this->userRepository->setToken($id->toString(), $token);
        
        $user = $this->userRepository->getUserById($id->toString());

        $this->sendEmail->sendEmail(
            ['to' => $user['email']], 
            [
                'template' => SendEmail::TEMPLATE_CONFIRM_EMAIL,
                'language' => $user['language'],
                'token'    => $user['token']
            ]
        );
        
        $logDto = $this->logDataTransformer->prepareLog(
            $id, 
            LogDto::ACTION_UPDATE, 
            self::USER_TABLE,
            null,
            [
                'id'    => $user['id'],
                'token' => $user['token']
            ]
        );
        
        $this->logManager->addLog($logDto);
    }
    
    /**
     * Generates random string from letters and digits
     * @param int $length
     * @return string
     */
    private function generateToken(int $length): string
    {
        $token = "";
        $codeAlphabet = implode(range('A', 'Z'));
        $codeAlphabet.= implode(range('a', 'z'));
        $codeAlphabet.= implode(range(0, 9));
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}