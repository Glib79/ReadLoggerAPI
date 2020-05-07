<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\UserBookDto;
use App\Repository\UserBookRepository;
use App\Service\AuthorBookManager;
use App\Service\BookManager;
use Ramsey\Uuid\Uuid;

class UserBookManager
{
    /**
     * @var AuthorBookManager
     */
    private $authorBookManager;
    
    /**
     * @var BookManager
     */
    private $bookManager;

    /**
     * @var UserBookRepository
     */
    private $userBookRepository;
    
    /**
     * UserBookManager constructor
     * @param AuthorBookManager $authorBookManager
     * @param BookManager $bookManager
     * @param UserBookRepository $userBookRepository
     */
    public function __construct(
        AuthorBookManager $authorBookManager,
        BookManager $bookManager,
        UserBookRepository $userBookRepository
    ) 
    {
        $this->authorBookManager = $authorBookManager;
        $this->bookManager = $bookManager;
        $this->userBookRepository = $userBookRepository;
    }

    /**
     * Create book for user from dto
     * @param UserBookDto $dto
     * @return string $id - created record id
     */
    public function createUserBook(UserBookDto $dto): string
    {
        if (!$dto->book->id) {
            $id = $this->bookManager->createBook($dto->book);
            $dto->book->id = Uuid::fromString($id);
        }
        return $this->userBookRepository->addBookToUser($dto);
    }
    
    /**
     * Delete user book
     * @param string $id
     * @param string $userId
     * @return void
     */
    public function deleteUserBook(string $id, string $userId): void
    {
        $this->userBookRepository->deleteUsersBook($id, $userId);
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
     * @return void
     */
    public function updateUserBook(UserBookDto $dto): void
    {
        $this->userBookRepository->updateUserBook($dto);
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
            switch ($key) {
                case substr($key, 0, 4) === 'book':
                    $row['book'][substr($key, 5)] = $val;
                    break;
                case substr($key, 0, 6) === 'format':
                    $row['format'][substr($key, 7)] = $val;
                    break;
                case substr($key, 0, 8) === 'language':
                    $row['language'][substr($key, 9)] = $val;
                    break;
                case substr($key, 0, 6) === 'status':
                    $row['status'][substr($key, 7)] = $val;
                    break;
                default:
                    $row[$key] = $val;
            }
        }
        
        return $row;
    }
}