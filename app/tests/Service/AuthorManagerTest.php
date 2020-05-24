<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\AuthorDto;
use App\DTO\LogDto;
use App\Repository\AuthorRepository;
use App\Service\AuthorManager;
use App\Service\LogManager;
use App\Tests\BaseTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving AuthorDto object
     * EXPECTED: save to database new Author 
     */
    public function testCreateAuthor()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $authorId = Uuid::uuid4();
        
        $authorDto = new AuthorDto($serializer, $validator);
        $authorDto->firstName = 'firstName_string';
        $authorDto->lastName = 'lastName_string';
        $authorDto->id = $authorId;
        
        $authorRepository = $this->createMock(AuthorRepository::class);
        $authorRepository->expects($this->once())
            ->method('addAuthor')
            ->with($authorDto)
            ->willReturn($authorId->toString()); 
        
        $userId = Uuid::uuid4();
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_CREATE;
        $logDto->table = 'author';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_CREATE, 'author', $authorDto)
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $authorManager = new AuthorManager($authorRepository, $logDataTransformer, $logManager);
        
        $result = $authorManager->createAuthor($authorDto, $userId);
        
        $this->assertSame($authorId->toString(), $result);
    }
}