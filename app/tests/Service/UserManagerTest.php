<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\UserDto;
use App\Repository\UserRepository;
use App\Service\UserManager;
use App\Support\User;
use App\Tests\BaseTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving User object
     * EXPECTED: return proper JWT token
     */
    public function testGenerateJWTToken()
    {
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        
        $encoder->expects($this->never())
            ->method($this->anything());
        
        $user = new User('test@test.com');
        
        $JWTManager = $this->createMock(JWTTokenManagerInterface::class);
        
        $JWTManager->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('token_string');
            
        $userRepository = $this->createMock(UserRepository::class);
        
        $userRepository->expects($this->never())
            ->method($this->anything());
        
        $userManager = new UserManager($encoder, $JWTManager, $userRepository);
        
        $result = $userManager->generateJWTToken($user);
        
        $this->assertSame('token_string', $result);
    }
    
    /**
     * SCENARIO: receiving email and password
     * EXPECTED: create and save User to database 
     */
    public function testCreateUser()
    {
        $user = new User('test@test.com');
        $user->setRoles(['ROLE_USER']);
        
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        
        $encoder->expects($this->once())
            ->method('encodePassword')
            ->with($user, 'password_string')
            ->willReturn('encoded_password_string');
            
        $JWTManager = $this->createMock(JWTTokenManagerInterface::class);
        
        $JWTManager->expects($this->never())
            ->method($this->anything());
        
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $userDtoRepository = new UserDto($serializer, $validator);
        $userDtoRepository->email = 'test@test.com';
        $userDtoRepository->password = 'encoded_password_string';
        $userDtoRepository->roles = ['ROLE_USER'];
        
        $userRepository = $this->createMock(UserRepository::class);
    
        $userRepository->expects($this->once())
            ->method('createUser')
            ->with($userDtoRepository)
            ->willReturn('id_string');
        
        $userDto = new UserDto($serializer, $validator);
        $userDto->email = 'test@test.com';
        $userDto->password = 'password_string';
          
        $userManager = new UserManager($encoder, $JWTManager, $userRepository);
        
        $result = $userManager->createUser($userDto);
        
        $this->assertSame('id_string', $result);
    }
}