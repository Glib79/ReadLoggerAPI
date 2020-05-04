<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\BaseDto;
use App\DTO\UserBookDto;
use DateTime;
use Doctrine\DBAL\ParameterType;
use Ramsey\Uuid\Uuid;

class UserBookRepository extends BaseRepository
{
    /**
     * Add new user-book connection to database
     * @param UserBookDto $dto
     * @return string $id - created record id
     */
    public function addBookToUser(UserBookDto $dto): string
    {
        $sql = <<<'SQL'
            INSERT INTO user_book (
                id, 
                user_id, 
                book_id, 
                status_id,
                start_date, 
                end_date,
                format_id,
                rating,
                language_id,
                notes,
                created_at, 
                modified_at
            ) 
            VALUES (
                :id, 
                :userId, 
                :bookId,
                :statusId,
                :startDate,
                :endDate,
                :formatId,
                :rating,
                :languageId,
                :notes,
                :createdAt, 
                :modifiedAt
            );
        SQL;
        
        $now = new DateTime();
        $id = Uuid::uuid4()->toString();
        
        $this->execute(
            $this->writeConn, 
            $sql,
            [
                'id'         => $id,
                'userId'     => $dto->userId->toString(),
                'bookId'     => $dto->book->id->toString(),
                'statusId'   => $dto->status->id,
                'startDate'  => $dto->startDate ? $dto->startDate->format(BaseDto::FORMAT_DATE_TIME_DB) : null,
                'endDate'    => $dto->endDate ? $dto->endDate->format(BaseDto::FORMAT_DATE_TIME_DB) : null,
                'formatId'   => $dto->format->id,
                'rating'     => $dto->rating,
                'languageId' => $dto->language->id,
                'notes'      => $dto->notes,
                'createdAt'  => $now->format(BaseDto::FORMAT_DATE_TIME_DB),
                'modifiedAt' => $now->format(BaseDto::FORMAT_DATE_TIME_DB)
            ]
        );
        
        return $id;
    }
    
    /**
     * Find books by user
     * @param string $userId
     * @param array $params 
     * @return array
     */
    public function findBooksByUser(string $userId, array $params = []): array
    {
        $params['limit'] = $params['limit'] ?? 10;
        $params['offset'] = isset($params['page']) ? $params['page'] * $params['limit'] : 0; 

        $sql = <<<'SQL'
            SELECT 
                ub.*, 
                b.title AS book_title,
                b.sub_title AS book_sub_title,
                b.size AS book_size,
                b.created_at AS book_created_at,
                b.modified_at AS book_modified_at,
                f.translation_key AS format_translation_key,
                l.translation_key AS language_translation_key,
                s.translation_key AS status_translation_key
            FROM user_book AS ub 
            JOIN book AS b ON (b.id = ub.book_id)
            JOIN format AS f ON (f.id = ub.format_id)
            JOIN language AS l ON (l.id = ub.language_id)
            JOIN status AS s ON (s.id = ub.status_id)
            WHERE ub.user_id = :id
            ORDER BY ub.end_date DESC
            LIMIT :offset, :limit;
        SQL;
        
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql, 
            [
                'id'     => $userId,
                'offset' => (int) $params['offset'],
                'limit'  => (int) $params['limit']
            ],
            [
                'id'     => ParameterType::STRING,
                'offset' => ParameterType::INTEGER,
                'limit'  => ParameterType::INTEGER
            ]
        );
        
        return $stmt->fetchAll();
    }
}

