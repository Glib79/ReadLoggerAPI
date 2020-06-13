<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\LogDto;
use App\DTO\UserDto;
use App\Repository\UserRepository;
use App\Service\LogManager;
use App\Service\UserManager;
use App\Support\SendEmail;
use App\Support\User;
use App\Tests\BaseTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Ramsey\Uuid\Uuid;
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

        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->never())
            ->method($this->anything()); 

        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->never())
            ->method($this->anything());
        
        $sendEmail = $this->createMock(SendEmail::class);
        $sendEmail->expects($this->never())
            ->method($this->anything());
        
        $userManager = new UserManager(
            $encoder, 
            $JWTManager,
            $logDataTransformer,
            $logManager,
            $userRepository,
            $sendEmail
        );
        
        $result = $userManager->generateJWTToken($user);
        
        $this->assertSame('token_string', $result);
    }
    
    /**
     * SCENARIO: receiving User id
     * EXPECTED: set up in db isConfirmed for true for given user
     */
    public function testConfirmEmail()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $userId = Uuid::uuid4();

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder->expects($this->never())
            ->method($this->anything());
        
        $JWTManager = $this->createMock(JWTTokenManagerInterface::class);
        $JWTManager->expects($this->never())
            ->method($this->anything());
            
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('confirmEmail')
            ->with($userId->toString());

        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_CONFIRM_EMAIL;
        $logDto->table = 'user';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_CONFIRM_EMAIL, 'user', null, [
                'id'          => $userId->toString(),
                'isConfirmed' => true,
                'token'       => ''
            ])
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $sendEmail = $this->createMock(SendEmail::class);
        $sendEmail->expects($this->never())
            ->method($this->anything());
        
        $userManager = new UserManager(
            $encoder, 
            $JWTManager,
            $logDataTransformer,
            $logManager,
            $userRepository,
            $sendEmail
        );
        
        $userManager->confirmEmail($userId->toString());
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

        $userId = Uuid::uuid4();
        
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('createUser')
            ->willReturn($userId->toString());
        
        $userDto = new UserDto($serializer, $validator);
        $userDto->id = $userId;
        $userDto->email = 'test@test.com';
        $userDto->password = 'password_string';
          
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog');
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog');

        $sendEmail = $this->createMock(SendEmail::class);
        $sendEmail->expects($this->once())
            ->method('sendEmail');
        
        $userManager = new UserManager(
            $encoder, 
            $JWTManager,
            $logDataTransformer,
            $logManager,
            $userRepository,
            $sendEmail
        );
        
        $result = $userManager->createUser($userDto);
        
        $this->assertSame($userId->toString(), $result);
    }
    
    /**
     * SCENARIO: receiving User id
     * EXPECTED: generate new token store it in db and send new email to user
     */
    public function testResendConfirmationEmail()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $userId = Uuid::uuid4();

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder->expects($this->never())
            ->method($this->anything());
        
        $JWTManager = $this->createMock(JWTTokenManagerInterface::class);
        $JWTManager->expects($this->never())
            ->method($this->anything());
            
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('setToken');
        $userRepository->expects($this->once())
            ->method('getUserById')
            ->with($userId->toString())
            ->willReturn([
                'id'       => $userId->toString(),
                'email'    => 'test@test.com',
                'language' => 'en',
                'token'    => 'token_string'
            ]);

        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_UPDATE;
        $logDto->table = 'user';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_UPDATE, 'user', null, [
                'id'    => $userId->toString(),
                'token' => 'token_string'
            ])
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $sendEmail = $this->createMock(SendEmail::class);
        $sendEmail->expects($this->once())
            ->method('sendEmail')
            ->with(['to' => 'test@test.com'], 
            [
                'template' => SendEmail::TEMPLATE_CONFIRM_EMAIL,
                'language' => 'en',
                'token'    => 'token_string'
            ]);
        
        $userManager = new UserManager(
            $encoder, 
            $JWTManager,
            $logDataTransformer,
            $logManager,
            $userRepository,
            $sendEmail
        );
        
        $userManager->resendConfirmationEmail($userId);
    }
}