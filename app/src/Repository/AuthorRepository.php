<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\AuthorDto;
use App\DTO\BaseDto;
use DateTime;
use Doctrine\DBAL\ParameterType;
use Ramsey\Uuid\Uuid;

class AuthorRepository extends BaseRepository
{
    /**
     * Add new author to database
     * @param AuthorDto $dto
     * @return string $id - created record id
     */
    public function addAuthor(AuthorDto $dto): string
    {
        $sql = 'INSERT INTO author (id, first_name, last_name, created_at, modified_at) 
            VALUES (:id, :firstName, :lastName, :createdAt, :modifiedAt);';
        
        $now = new DateTime();
        $id = Uuid::uuid4()->toString();
        
        $this->execute(
            $this->writeConn, 
            $sql,
            [
                'id'         => $id,
                'firstName'  => $dto->firstName,
                'lastName'   => $dto->lastName,
                'createdAt'  => $now->format(BaseDto::FORMAT_DATE_TIME_DB),
                'modifiedAt' => $now->format(BaseDto::FORMAT_DATE_TIME_DB)
            ]
        );
        
        return $id;
    }
    
    /**
     * Finds authors by query
     * @param string $query
     * @param array $params
     * @return array
     */
    public function findAuthorsByQuery(string $query, array $params = []): array
    {
        $params['limit'] = $params['limit'] ?? 10;

        $sql = <<<'SQL'
            SELECT a.* 
            FROM author AS a 
            WHERE LOWER(a.first_name) LIKE :query 
            OR LOWER(a.last_name) LIKE :query
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

