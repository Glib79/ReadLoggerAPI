<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\BookDto;
use App\DTO\StatusDto;
use App\DTO\UserBookDto;
use App\Repository\UserBookRepository;
use App\Service\AuthorBookManager;
use App\Service\BookManager;
use App\Service\UserBookManager;
use App\Tests\BaseTestCase;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserBookManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving UserBookDto object with existing book
     * EXPECTED: save to database new UserBook 
     */
    public function testCreateUserBookWithExistingBook()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookId = Uuid::uuid4();
        $bookDto = new BookDto($serializer, $validator);
        $bookDto->id = $bookId;
        
        $statusDto = new StatusDto($serializer, $validator);
        $statusDto->id = StatusDto::STATUS_PLANNED;
        
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->book = $bookDto;
        $userBookDto->status = $statusDto;
        
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('addBookToUser')
            ->with($userBookDto)
            ->willReturn('id_string');
        
        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);
        
        $result = $userBookManager->createUserBook($userBookDto);
        
        $this->assertSame('id_string', $result);
    }
    
    /**
     * SCENARIO: receiving UserBookDto object with new book
     * EXPECTED: save to database new UserBook 
     */
    public function testCreateUserBookWithNewBook()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookDto = new BookDto($serializer, $validator);
        $bookDto->title = 'title_string';
        
        $statusDto = new StatusDto($serializer, $validator);
        $statusDto->id = StatusDto::STATUS_PLANNED;
        
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->book = $bookDto;
        $userBookDto->status = $statusDto;
        
        $bookId = Uuid::uuid4()->toString();
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->once())
            ->method('createBook')
            ->with($bookDto)
            ->willReturn($bookId);   
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('addBookToUser')
            ->with($userBookDto)
            ->willReturn('id_string');
        
        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);
        
        $result = $userBookManager->createUserBook($userBookDto);
        
        $this->assertSame('id_string', $result);
    }
    
    /**
     * SCENARIO: receiving id and userId
     * EXPECTED: delete from database UserBook with given id and userId 
     */
    public function testDeleteUserBook()
    {
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('deleteUsersBook')
            ->with('id_string', 'userId_string');
        
        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);
        
        $userBookManager->deleteUserBook('id_string', 'userId_string');
    }
    
    /**
     * SCENARIO: receiving userId
     * EXPECTED: find userBooks using given userId 
     */
    public function testFindUsersBooks()
    {
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->once())
            ->method('findAuthorsByBooks')
            ->with(['bookId_string'])
            ->willReturn(['bookId_string' => [
                ['id' => 'authorId_string']
            ]]); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('findBooksByUser')
            ->with('userId_string', [])
            ->willReturn([
                [
                    'id'          => 'id_string',
                    'book_id'     => 'bookId_string'
                ]
            ]);
        
        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);
        
        $return = $userBookManager->findUsersBooks('userId_string');
        
        $this->assertEqualsCanonicalizing($return, [
            [
                'id'       => 'id_string',
                'book'     => [
                    'id'      => 'bookId_string',
                    'authors' => [
                        [
                            'id' => 'authorId_string'
                        ]
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * SCENARIO: receiving id
     * EXPECTED: find userBook using given id 
     */
    public function testGetUsersBookById()
    {
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->once())
            ->method('findAuthorsByBooks')
            ->with(['bookId_string'])
            ->willReturn(['bookId_string' => [
                ['id' => 'authorId_string']
            ]]); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('findBookById')
            ->with('id_string')
            ->willReturn([
                'id'          => 'id_string',
                'book_id'     => 'bookId_string'
            ]);
        
        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);
        
        $return = $userBookManager->getUsersBookById('id_string');
        
        $this->assertEqualsCanonicalizing($return, [
            'id'       => 'id_string',
            'book'     => [
                'id'      => 'bookId_string',
                'authors' => [
                    [
                        'id' => 'authorId_string'
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * SCENARIO: receiving UserBookDto
     * EXPECTED: update in database proper record 
     */
    public function testUpdateUserBook()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   

        $statusDto = new StatusDto($serializer, $validator);
        $statusDto->id = StatusDto::STATUS_PLANNED;
        
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->id = 'id_string';
        $userBookDto->status = $statusDto;

        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('updateUserBook')
            ->with($userBookDto);
        
        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);
        
        $userBookManager->updateUserBook($userBookDto);
    }
    
    /**
     * SCENARIO: receiving UserBookDto with status
     * EXPECTED: remove from UserBookDto unnecessary dates
     */
    public function testCleanDatesByStatus()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   

        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->never())
            ->method($this->anything());

        $statusDto = new StatusDto($serializer, $validator);
        $statusDto->id = StatusDto::STATUS_FINISHED;
        
        $now = new DateTime();
        
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->id = 'id_string';
        $userBookDto->status = $statusDto;
        $userBookDto->startDate = $now;
        $userBookDto->endDate = $now;

        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);

        $this->callMethod($userBookManager, 'cleanDatesByStatus', [$userBookDto]);
        
        // For status = finished both dates should be present
        $this->assertEqualsCanonicalizing($userBookDto->startDate, $now);
        $this->assertEqualsCanonicalizing($userBookDto->endDate, $now);
        
        $userBookDto->status->id = StatusDto::STATUS_DURING;
        $this->callMethod($userBookManager, 'cleanDatesByStatus', [$userBookDto]);
        
        // For status = during endDate should be ereased
        $this->assertEqualsCanonicalizing($userBookDto->startDate, $now);
        $this->assertNull($userBookDto->endDate);

        $userBookDto->status->id = StatusDto::STATUS_ABANDONED;
        $userBookDto->endDate = $now;
        $this->callMethod($userBookManager, 'cleanDatesByStatus', [$userBookDto]);

        // For status = abandoned endDate should be ereased
        $this->assertEqualsCanonicalizing($userBookDto->startDate, $now);
        $this->assertNull($userBookDto->endDate);

        $userBookDto->status->id = StatusDto::STATUS_PLANNED;
        $userBookDto->endDate = $now;
        $this->callMethod($userBookManager, 'cleanDatesByStatus', [$userBookDto]);

        // For status = planned both should be ereased
        $this->assertNull($userBookDto->startDate);
        $this->assertNull($userBookDto->endDate);
    }
    
    /**
     * SCENARIO: receiving flat array from database
     * EXPECTED: convert to object like array
     */
    public function testPrepareUserBook()
    {
        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   

        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->never())
            ->method($this->anything());

        $inputArray = [
            'id'                     => 'id_string',
            'book_id'                => 'bookId_string',
            'format_id'              => 1,
            'language_id'            => 2,
            'status_id'              => 3,
            'status_translation_key' => 'translation.key_string'
        ];

        $userBookManager = new UserBookManager($authorBookManager, $bookManager, $userBookRepository);

        $result = $this->callMethod($userBookManager, 'prepareUserBook', [$inputArray]);
        
        $this->assertEqualsCanonicalizing($result, [
            'id'       => 'id_string',
            'book'     => [
                'id'      => 'bookId_string',
            ],
            'format'   => ['id' => 1],
            'language' => ['id' => 2],
            'status'   => [
                'id'              => 3,
                'translation_key' => 'translation.key_string'
            ]
        ]);
    }
}