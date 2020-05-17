<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\UserDto;
use App\Repository\UserRepository;
use App\Support\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    public const ROLE_USER = 'ROLE_USER';
    
    /**
     * @var UserPasswordEncoderInterface
     */
    private $ecncoder;
    
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTManager;
    
    /**
     * @var UserRepository
     */
    private $userRepository;
    
    /**
     * UserManager constructor
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTTokenManagerInterface $JWTManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        JWTTokenManagerInterface $JWTManager, 
        UserRepository $userRepository
    )
    {
        $this->encoder = $encoder;
        $this->JWTManager = $JWTManager;
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
        
        $user = new User($dto->email);
        $user->setRoles($dto->roles);
        
        $dto->password = $this->encoder->encodePassword($user, $dto->password);
        
        return $this->userRepository->createUser($dto);
    }
}