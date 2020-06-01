<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\BookDto;
use Doctrine\DBAL\ParameterType;
use Ramsey\Uuid\Uuid;

class BookRepository extends BaseRepository
{
    /**
     * Add new book to database
     * @param BookDto $dto
     * @return string $id - created record id
     */
    public function addBook(BookDto $dto): string
    {
        $sql = 'INSERT INTO book (id, title, sub_title, size) 
            VALUES (:id, :title, :subTitle, :size);';
        
        $id = Uuid::uuid4()->toString();
        
        $this->execute(
            $this->writeConn, 
            $sql,
            [
                'id'         => $id,
                'title'      => $dto->title,
                'subTitle'   => $dto->subTitle,
                'size'       => $dto->size
            ]
        );
        
        return $id;
    }
    
    /**
     * Finds books by query
     * @param string $query
     * @param array $params
     * @return array
     */
    public function findBooksByQuery(string $query, array $params = []): array
    {
        $params['limit'] = $params['limit'] ?? 10;

        $sql = <<<'SQL'
            SELECT b.* 
            FROM book AS b 
            WHERE LOWER(b.title) LIKE :query
            LIMIT :limit;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql, 
            [
                'query' => sprintf('%%%s%%', strtolower($query)),
                'limit' => (int) $params['limit']
            ],
            [
                'query'  => ParameterType::STRING,
                'limit'  => ParameterType::INTEGER
            ]
        );
        
        return $stmt->fetchAll();
    }
}

