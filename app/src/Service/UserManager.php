<?php
declare(strict_types=1);

namespace App\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\LogDto;
use App\DTO\UserDto;
use App\Repository\UserRepository;
use App\Service\LogManager;
use App\Support\User;
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
    private $ecncoder;
    
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
     * UserManager constructor
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTTokenManagerInterface $JWTManager
     * @param LogDataTransformer $logDataTransformer
     * @param LogManager $logManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        JWTTokenManagerInterface $JWTManager, 
        LogDataTransformer $logDataTransformer,
        LogManager $logManager,
        UserRepository $userRepository
    )
    {
        $this->encoder = $encoder;
        $this->JWTManager = $JWTManager;
        $this->logDataTransformer = $logDataTransformer;
        $this->logManager = $logManager;
        $this->userRepository = $userRepository;
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
     * Create User fro DTO
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