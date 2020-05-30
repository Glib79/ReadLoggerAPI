<?php
declare(strict_types=1);

namespace App\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\LogDto;
use App\DTO\StatusDto;
use App\DTO\UserBookDto;
use App\Repository\UserBookRepository;
use App\Service\AuthorBookManager;
use App\Service\BookManager;
use App\Service\LogManager;
use Ramsey\Uuid\Uuid;

class UserBookManager
{
    public const USER_BOOK_TABLE = 'user_book';

    private const INTERNAL_OBJECT_LIST = [
        'book', 
        'format', 
        'language', 
        'status'
    ];
    
    private const REMOVE_FIELDS_FROM_LOG = ['book'];


    /**
     * @var AuthorBookManager
     */
    private $authorBookManager;
    
    /**
     * @var BookManager
     */
    private $bookManager;
 
    /**
     * @var LogDataTransformer
     */
    private $logDataTransformer;
 
    /**
     * @var LogManager
     */
    private $logManager;
    
    /**
     * @var UserBookRepository
     */
    private $userBookRepository;
    
    /**
     * UserBookManager constructor
     * @param AuthorBookManager $authorBookManager
     * @param BookManager $bookManager
     * @param LogDataTransformer $logDataTransformer
     * @param LogManager $logManager
     * @param UserBookRepository $userBookRepository
     */
    public function __construct(
        AuthorBookManager $authorBookManager,
        BookManager $bookManager,
        LogDataTransformer $logDataTransformer,
        LogManager $logManager,
        UserBookRepository $userBookRepository
    ) 
    {
        $this->authorBookManager = $authorBookManager;
        $this->bookManager = $bookManager;
        $this->logDataTransformer = $logDataTransformer;
        $this->logManager = $logManager;
        $this->userBookRepository = $userBookRepository;
    }

    /**
     * Create book for user from DTO
     * @param UserBookDto $dto
     * @return string $id - created record id
     */
    public function createUserBook(UserBookDto $dto): string
    {
        if (!$dto->book->id) {
            $id = $this->bookManager->createBook($dto->book, $dto->userId);
            $dto->book->id = Uuid::fromString($id);
        }
        $this->cleanDatesByStatus($dto);
        
        $id = $this->userBookRepository->addBookToUser($dto);
        $dto->id = Uuid::fromString($id);
        
        $logDto = $this->logDataTransformer->prepareLog(
            $dto->userId, 
            LogDto::ACTION_CREATE, 
            self::USER_BOOK_TABLE, 
            $dto
        );
        
        $this->logManager->addLog($logDto);
        
        return $id;
    }
    
    /**
     * Delete user book
     * @param array $userBook
     * @param Uuid $userId
     * @return void
     */
    public function deleteUserBook(array $userBook, Uuid $userId): void
    {
        $this->userBookRepository->deleteUsersBook($userBook['id'], $userId->toString());
        
        $logDto = $this->logDataTransformer->prepareLog(
            $userId, 
            LogDto::ACTION_DELETE, 
            self::USER_BOOK_TABLE,
            null,
            $userBook
        );
        
        $this->logManager->addLog($logDto);
    }
    
    /**
     * Finds users books
     * @param string $userId
     * @param array $params
     * @return array
     */
    public function findUsersBooks(string $userId, array $params = []): array
    {
        $data = $this->userBookRepository->findBooksByUser($userId, $params);
        
        $output = [];
        $bookIds = [];
        
        foreach ($data as $userBook) {
            $bookIds[] = $userBook['book_id'];
            $output[] = $this->prepareUserBook($userBook);
        }
        
        $authors = $this->authorBookManager->findAuthorsByBooks($bookIds);
        
        foreach ($output as $key=>$userBook) {
            $output[$key]['book']['authors'] = $authors[$userBook['book']['id']];
        }
        
        return $output;
    }
    
    /**
     * Get users book by id
     * @param string $id
     * @return array
     */
    public function getUsersBookById(string $id): array
    {
        $data = $this->userBookRepository->findBookById($id);
        $output = $this->prepareUserBook($data);
        
        $authors = $this->authorBookManager->findAuthorsByBooks([$output['book']['id']]);
        $output['book']['authors'] = $authors[$output['book']['id']];
        
        return $output;
    }
    
    /**
     * Update user book using DTO
     * @param UserBookDto $dto
     * @param array $oldUserBook
     * @return void
     */
    public function updateUserBook(UserBookDto $dto, array $oldUserBook): void
    {
        $this->cleanDatesByStatus($dto);
        $this->userBookRepository->updateUserBook($dto);
        
        $logDto = $this->logDataTransformer->prepareLog(
            $dto->userId, 
            LogDto::ACTION_UPDATE, 
            self::USER_BOOK_TABLE, 
            $dto,
            $oldUserBook,
            self::REMOVE_FIELDS_FROM_LOG
        );
        
        $this->logManager->addLog($logDto);
    }
    
    /**
     * Clean dates using status
     * @param UserBookDto $dto
     */
    private function cleanDatesByStatus(UserBookDto $dto): void
    {
        if (!in_array($dto->status->id, StatusDto::STATUSES_WITH_START_DATE)) {
            $dto->startDate = null;
        }
        if (!in_array($dto->status->id, StatusDto::STATUSES_WITH_END_DATE)) {
            $dto->endDate = null;
        }
    }
    
    /**
     * Prepare users book array
     * @param array $userBook
     * @return array
     */
    private function prepareUserBook(array $userBook): array
    {
        $row = [];

        foreach ($userBook as $key => $val) {
            $keyArray = explode('_', $key);
            if (in_array($keyArray[0], self::INTERNAL_OBJECT_LIST)) {
                $newKey = array_shift($keyArray);
                $row[$newKey][implode('_', $keyArray)] = $val;
                
                continue;
            }
            
            $row[$key] = $val;
        }
        
        return $row;
    }
}