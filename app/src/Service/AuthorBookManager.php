<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\AuthorBookRepository;

class AuthorBookManager
{
    /**
     * @var AuthorBookRepository
     */
    private $authorBookRepository;
    
    /**
     * AuthorBookManager constructor
     * @param AuthorBookRepository $authorBookRepository
     */
    public function __construct(AuthorBookRepository $authorBookRepository)
    {
        $this->authorBookRepository = $authorBookRepository;
    }

    /**
     * Finds authors for provided books
     * @param array $bookIds
     * @return array - bookId => [ Authors List ]
     */
    public function findAuthorsByBooks(array $bookIds): array
    {
        $authors = [];
        if ($bookIds) {
            $authors_data = $this->authorBookRepository->findAuthorsByBooks($bookIds);
        
            foreach ($authors_data as $author) {
                $authors[$author['book_id']][] = $author;
            }
        }
        
        return $authors;
    }
}