<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\AuthorDto;
use App\DTO\BookDto;
use Doctrine\DBAL\Connection;

class AuthorBookRepository extends BaseRepository
{
    /**
     * Add new author-book connection to database
     * @param BookDto $dto
     */
    public function addAuthorsToBook(BookDto $dto): void
    {
        $sql = 'INSERT INTO author_book (author_id, book_id) 
            VALUES ';
        
        $bookId = $dto->id->toString();
        
        /**
         * @var AuthorDto $author
         */
        foreach ($dto->authors as $author) {
            $sql .= sprintf('("%s", "%s"),', $author->id->toString(), $bookId);
        }
        $sql = rtrim($sql, ',');
        $sql .= ';';
        
        $this->execute(
            $this->writeConn, 
            $sql
        );
    }
    
    /**
     * Find authors by books
     * @param array $bookIds
     * @return array
     */
    public function findAuthorsByBooks(array $bookIds): array
    {
        $sql = <<<'SQL'
            SELECT 
                ab.book_id, 
                ab.author_id AS id, 
                a.first_name, 
                a.last_name,
                a.created_at,
                a.modified_at
            FROM author_book AS ab
            JOIN author AS a ON (a.id = ab.author_id)
            WHERE ab.book_id IN (:books);
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql, 
            [
                'books' => implode('","',$bookIds)
            ]
        );
        
        return $stmt->fetchAll();
    }
}

