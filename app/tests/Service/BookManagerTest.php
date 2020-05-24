<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\AuthorDto;
use App\DTO\BookDto;
use App\DTO\LogDto;
use App\Repository\AuthorBookRepository;
use App\Repository\BookRepository;
use App\Service\AuthorBookManager;
use App\Service\AuthorManager;
use App\Service\BookManager;
use App\Service\LogManager;
use App\Tests\BaseTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving BookDto object with existing author
     * EXPECTED: save to database new Book 
     */
    public function testCreateBookWithExistingAuthor()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 

        $authorManager = $this->createMock(AuthorManager::class);
        $authorManager->expects($this->never())
            ->method($this->anything());  
        
        $authorId = Uuid::uuid4();
        $authorDto = new AuthorDto($serializer, $validator);
        $authorDto->id = $authorId;
        
        $bookId = Uuid::uuid4()->toString();
        $bookDto = new BookDto($serializer, $validator);
        $bookDto->authors = [$authorDto];
        
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('addBook')
            ->with($bookDto)
            ->willReturn($bookId); 
        
        $authorBookRepository = $this->createMock(AuthorBookRepository::class);
        $authorBookRepository->expects($this->once())
            ->method('addAuthorsToBook')
            ->with($bookDto);
        
        $userId = Uuid::uuid4();
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_CREATE;
        $logDto->table = 'book';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_CREATE, 'book', $bookDto)
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $bookManager = new BookManager(
            $authorBookManager, 
            $authorManager, 
            $authorBookRepository, 
            $bookRepository,
            $logDataTransformer,
            $logManager
        );
        
        $result = $bookManager->createBook($bookDto, $userId);
        
        $this->assertSame($bookId, $result);
    }
    
    /**
     * SCENARIO: receiving BookDto object with new author
     * EXPECTED: save to database new Book 
     */
    public function testCreateBookWithNewAuthor()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 

        $authorId = Uuid::uuid4();
        $authorDto = new AuthorDto($serializer, $validator);
        $authorDto->firstName = 'firstName_string';
        $authorDto->lastName = 'lastName_string';

        $authorManager = $this->createMock(AuthorManager::class);
        $authorManager->expects($this->once())
            ->method('createAuthor')
            ->with($authorDto)
            ->willReturn($authorId);  
        
        $bookId = Uuid::uuid4()->toString();
        $bookDto = new BookDto($serializer, $validator);
        $bookDto->authors = [$authorDto];
        
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('addBook')
            ->with($bookDto)
            ->willReturn($bookId); 
        
        $authorBookRepository = $this->createMock(AuthorBookRepository::class);
        $authorBookRepository->expects($this->once())
            ->method('addAuthorsToBook')
            ->with($bookDto);
        
        $userId = Uuid::uuid4();
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_CREATE;
        $logDto->table = 'book';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_CREATE, 'book', $bookDto)
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $bookManager = new BookManager(
            $authorBookManager, 
            $authorManager, 
            $authorBookRepository, 
            $bookRepository,
            $logDataTransformer,
            $logManager
        );
        
        $result = $bookManager->createBook($bookDto, $userId);
        
        $this->assertSame($bookId, $result);
    }
    
    /**
     * SCENARIO: receiving query string
     * EXPECTED: returns list of matching books 
     */
    public function testFindBooksByQuery()
    {
        $authorManager = $this->createMock(AuthorManager::class);
        $authorManager->expects($this->never())
            ->method($this->anything());   
        
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('findBooksByQuery')
            ->with('guery_string')
            ->willReturn([
                ['id' => 'bookId_string']
            ]); 

        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->once())
            ->method('findAuthorsByBooks')
            ->with(['bookId_string'])
            ->willReturn([
                'bookId_string' => [
                    ['id' => 'authorId_string']
                ]
            ]);
        
        $authorBookRepository = $this->createMock(AuthorBookRepository::class);
        $authorBookRepository->expects($this->never())
            ->method($this->anything()); 
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->never())
            ->method($this->anything()); 

        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->never())
            ->method($this->anything()); 
        
        $bookManager = new BookManager(
            $authorBookManager, 
            $authorManager, 
            $authorBookRepository, 
            $bookRepository,
            $logDataTransformer,
            $logManager
        );
        
        $result = $bookManager->findBooksByQuery('guery_string');

        $this->assertEqualsCanonicalizing($result, [
            [
                'id'      => 'bookId_string',
                'authors' => [
                    ['id' => 'authorId_string']
                ]
            ]
        ]);
    }
}