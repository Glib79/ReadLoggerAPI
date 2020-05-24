<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\BookDto;
use App\DTO\LogDto;
use App\DTO\StatusDto;
use App\DTO\UserBookDto;
use App\Repository\UserBookRepository;
use App\Service\AuthorBookManager;
use App\Service\BookManager;
use App\Service\LogManager;
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
        
        $userId = Uuid::uuid4();
        $userBookId = Uuid::uuid4();
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->id = $userBookId;
        $userBookDto->book = $bookDto;
        $userBookDto->userId = $userId;
        $userBookDto->status = $statusDto;
        
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());   
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('addBookToUser')
            ->with($userBookDto)
            ->willReturn($userBookId->toString());
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_CREATE;
        $logDto->table = 'user_book';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_CREATE, 'user_book', $userBookDto)
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );
        
        $result = $userBookManager->createUserBook($userBookDto);
        
        $this->assertSame($userBookId->toString(), $result);
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
        
        $userId = Uuid::uuid4();
        $userBookId = Uuid::uuid4();
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->id = $userBookId;
        $userBookDto->book = $bookDto;
        $userBookDto->userId = $userId;
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
            ->willReturn($userBookId->toString());
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_CREATE;
        $logDto->table = 'user_book';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_CREATE, 'user_book', $userBookDto)
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );
        
        $result = $userBookManager->createUserBook($userBookDto);
        
        $this->assertSame($userBookId->toString(), $result);
    }
    
    /**
     * SCENARIO: receiving id and userId
     * EXPECTED: delete from database UserBook with given id and userId 
     */
    public function testDeleteUserBook()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $authorBookManager = $this->createMock(AuthorBookManager::class);
        $authorBookManager->expects($this->never())
            ->method($this->anything()); 
                
        $bookManager = $this->createMock(BookManager::class); 
        $bookManager->expects($this->never())
            ->method($this->anything());
        
        $userId = Uuid::uuid4();
        $userBookId = Uuid::uuid4();
        $userBook = [
            'id'     => $userBookId->toString(),
            'userId' => $userId->toString()
        ];
        
        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('deleteUsersBook')
            ->with($userBook['id'], $userId->toString());
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_DELETE;
        $logDto->table = 'user_book';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with($userId, LogDto::ACTION_DELETE, 'user_book', null, $userBook)
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );
        
        $userBookManager->deleteUserBook($userBook, $userId);
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
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->never())
            ->method($this->anything()); 

        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->never())
            ->method($this->anything());         
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );
        
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
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->never())
            ->method($this->anything()); 

        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->never())
            ->method($this->anything());         
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );
        
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
        
        $userId = Uuid::uuid4();
        $userBookId = Uuid::uuid4();
        $oldUserBook = [
            'id'     => $userBookId->toString(),
            'userId' => $userId->toString()
        ];
        
        $userBookDto = new UserBookDto($serializer, $validator);
        $userBookDto->id = $userBookId;
        $userBookDto->userId = $userId;
        $userBookDto->status = $statusDto;

        $userBookRepository = $this->createMock(UserBookRepository::class);
        $userBookRepository->expects($this->once())
            ->method('updateUserBook')
            ->with($userBookDto);
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->userId = $userId;
        $logDto->action = LogDto::ACTION_UPDATE;
        $logDto->table = 'user_book';
        
        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->once())
            ->method('prepareLog')
            ->with(
                $userId, 
                LogDto::ACTION_UPDATE, 
                'user_book', 
                $userBookDto, 
                $oldUserBook, 
                ['book']
            )
            ->willReturn($logDto);
        
        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->once())
            ->method('addLog')
            ->with($logDto);
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );
        
        $userBookManager->updateUserBook($userBookDto, $oldUserBook);
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

        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->never())
            ->method($this->anything()); 

        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->never())
            ->method($this->anything());         
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );

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

        $logDataTransformer = $this->createMock(LogDataTransformer::class);
        $logDataTransformer->expects($this->never())
            ->method($this->anything()); 

        $logManager = $this->createMock(LogManager::class);
        $logManager->expects($this->never())
            ->method($this->anything());         
        
        $userBookManager = new UserBookManager(
            $authorBookManager, 
            $bookManager, 
            $logDataTransformer,
            $logManager,
            $userBookRepository
        );

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