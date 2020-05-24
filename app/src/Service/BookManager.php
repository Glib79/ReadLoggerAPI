<?php
declare(strict_types=1);

namespace App\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\AuthorDto;
use App\DTO\BookDto;
use App\DTO\LogDto;
use App\Repository\AuthorBookRepository;
use App\Repository\BookRepository;
use App\Service\AuthorBookManager;
use App\Service\AuthorManager;
use App\Service\LogManager;
use Ramsey\Uuid\Uuid;

class BookManager
{
    private const BOOK_TABLE = 'book';
    
    /**
     * @var AuthorBookManager
     */
    private $authorBookManager;

    /**
     * @var AuthorManager
     */
    private $authorManager;

    /**
     * @var AuthorBookRepository
     */
    private $authorBookRepository;

    /**
     * @var BookRepository
     */
    private $bookRepository;

    /**
     * @var LogDataTransformer
     */
    private $logDataTransformer;
 
    /**
     * @var LogManager
     */
    private $logManager;
    
    /**
     * BookManager constructor
     * @param AuthorBookManager $authorBookManager
     * @param AuthorManager $authorManager
     * @param AuthorBookRepository $authorBookRepository
     * @param BookRepository $bookRepository
     * @param LogDataTransformer $logDataTransformer
     * @param LogManager $logManager
     */
    public function __construct(
        AuthorBookManager $authorBookManager,
        AuthorManager $authorManager,
        AuthorBookRepository $authorBookRepository,
        BookRepository $bookRepository,
        LogDataTransformer $logDataTransformer,
        LogManager $logManager
    )
    {
        $this->authorBookManager = $authorBookManager;
        $this->authorManager = $authorManager;
        $this->authorBookRepository = $authorBookRepository;
        $this->bookRepository = $bookRepository;
        $this->logDataTransformer = $logDataTransformer;
        $this->logManager = $logManager;
    }

    /**
     * Create book from DTO
     * @param BookDto $dto
     * @param Uuid $userId
     * @return string $id - created record id
     */
    public function createBook(BookDto $dto, Uuid $userId): string
    {
        /* Add authors if necessary */
        if ($dto->authors) {
            /**
             * @var AuthorDto $author 
             */
            foreach ($dto->authors as $author) {
                if (!$author->id) {
                    $id = $this->authorManager->createAuthor($author, $userId);
                    $author->id = Uuid::fromString($id);
                }
            }
        }
        $id = $this->bookRepository->addBook($dto);
        $dto->id = Uuid::fromString($id);
        
        /* Connect authors with book */
        if ($dto->authors) {
            $this->authorBookRepository->addAuthorsToBook($dto);
        }
        
        $logDto = $this->logDataTransformer->prepareLog(
            $userId, 
            LogDto::ACTION_CREATE, 
            self::BOOK_TABLE, 
            $dto
        );
        
        $this->logManager->addLog($logDto);
        
        return $id;
    }
    
    /**
     * Finds books by query
     * @param string $query
     * @return array
     */
    public function findBooksByQuery(string $query): array
    {
        $books = $this->bookRepository->findBooksByQuery($query);
        $bookIds = [];
        
        foreach ($books as $book) {
            $bookIds[] = $book['id'];
        }
        
        $authors = $this->authorBookManager->findAuthorsByBooks($bookIds);
        
        foreach ($books as $key=>$book) {
            $books[$key]['authors'] = $authors[$book['id']];
        }
        
        return $books;
    }
}