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
     * Count users books
     * @param string $userId
     * @return int
     */
    public function countUsersBooks(string $userId): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(ub.id) as bookNumber
            FROM user_book AS ub
            WHERE ub.user_id = :userId
            GROUP BY ub.user_id;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql,
            [
                'userId' => $userId
            ]
        );
        
        return (int) $stmt->fetchColumn(0);
    }
    
    /**
     * Delete users book
     * @param string $id
     * @param string $userId
     * @return void
     */
    public function deleteUsersBook(string $id, string $userId): void
    {
        $sql = <<<'SQL'
            DELETE FROM user_book
            WHERE id = :id
            AND user_id = :userId;
        SQL;
        
        $this->execute(
            $this->writeConn, 
            $sql,
            [
                'id'     => $id,
                'userId' => $userId
            ]
        );
    }
    
    /**
     * Find user book by id
     * @param string $id
     * @return array
     */
    public function findBookById(string $id): array
    {
        $sql = <<<'SQL'
            SELECT 
                ub.*, 
                b.title AS book_title,
                b.sub_title AS book_sub_title,
                b.size AS book_size,
                f.translation_key AS format_translation_key,
                l.translation_key AS language_translation_key,
                s.translation_key AS status_translation_key
            FROM user_book AS ub 
            JOIN book AS b ON (b.id = ub.book_id)
            JOIN format AS f ON (f.id = ub.format_id)
            JOIN language AS l ON (l.id = ub.language_id)
            JOIN status AS s ON (s.id = ub.status_id)
            WHERE ub.id = :id;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql,
            [
                'id' => $id
            ]
        );
        
        return $stmt->fetch();
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
        $params['offset'] = !empty($params['page']) ? ($params['page'] - 1) * $params['limit'] : 0; 
        
        $sqlParams = [
            'id'     => $userId,
            'offset' => (int) $params['offset'],
            'limit'  => (int) $params['limit']
        ];
        
        $sqlTypes = [
            'id'     => ParameterType::STRING,
            'offset' => ParameterType::INTEGER,
            'limit'  => ParameterType::INTEGER
        ];
        
        $where = 'WHERE ub.user_id = :id ';
        if (!empty($params['status'])) {
            $where .= 'AND ub.status_id = :status ';
            $sqlParams['status'] = $params['status'];
            $sqlTypes['status'] = ParameterType::INTEGER;
        }

        $sql = <<<"SQL"
            SELECT 
                ub.id,
                ub.book_id,
                ub.format_id,
                ub.language_id,
                ub.status_id,
                ub.start_date,
                ub.end_date,
                b.title AS book_title,
                b.sub_title AS book_sub_title,
                b.size AS book_size,
                f.translation_key AS format_translation_key,
                l.translation_key AS language_translation_key,
                s.translation_key AS status_translation_key
            FROM user_book AS ub 
            JOIN book AS b ON (b.id = ub.book_id)
            JOIN format AS f ON (f.id = ub.format_id)
            JOIN language AS l ON (l.id = ub.language_id)
            JOIN status AS s ON (s.id = ub.status_id)
            $where
            ORDER BY ub.status_id ASC, ub.start_date DESC
            LIMIT :offset, :limit;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql, 
            $sqlParams,
            $sqlTypes
        );
        
        return $stmt->fetchAll();
    }
    
    /**
     * Update user-book table based on DTO
     * @param UserBookDto $dto
     * @return void
     */
    public function updateUserBook(UserBookDto $dto): void
    {
        $sql = <<<'SQL'
            UPDATE user_book 
            SET status_id = :statusId,
                start_date = :startDate,
                end_date = :endDate,
                format_id = :formatId,
                rating = :rating,
                language_id = :languageId,
                notes = :notes,
                modified_at = :modifiedAt
            WHERE id = :id
            AND user_id = :userId;
        SQL;
        
        $now = new DateTime();
                
        $this->execute(
            $this->writeConn, 
            $sql,
            [
                'id'         => $dto->id->toString(),
                'userId'     => $dto->userId->toString(),
                'statusId'   => $dto->status->id,
                'startDate'  => $dto->startDate ? $dto->startDate->format(BaseDto::FORMAT_DATE_TIME_DB) : null,
                'endDate'    => $dto->endDate ? $dto->endDate->format(BaseDto::FORMAT_DATE_TIME_DB) : null,
                'formatId'   => $dto->format->id,
                'rating'     => $dto->rating,
                'languageId' => $dto->language->id,
                'notes'      => $dto->notes,
                'modifiedAt' => $now->format(BaseDto::FORMAT_DATE_TIME_DB)
            ]
        );
    }
}

