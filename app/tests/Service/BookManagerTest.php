<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\AuthorDto;
use App\DTO\BookDto;
use App\Repository\AuthorBookRepository;
use App\Repository\BookRepository;
use App\Service\AuthorBookManager;
use App\Service\AuthorManager;
use App\Service\BookManager;
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
        
        $bookManager = new BookManager($authorBookManager, $authorManager, $authorBookRepository, $bookRepository);
        
        $result = $bookManager->createBook($bookDto);
        
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
        
        $bookManager = new BookManager($authorBookManager, $authorManager, $authorBookRepository, $bookRepository);
        
        $result = $bookManager->createBook($bookDto);
        
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
        
        $bookManager = new BookManager($authorBookManager, $authorManager, $authorBookRepository, $bookRepository);
        
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