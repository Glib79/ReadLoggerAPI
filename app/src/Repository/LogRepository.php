<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\BaseDto;
use App\DTO\LogDto;
use App\Service\UserBookManager;
use DateTime;
use Doctrine\DBAL\ParameterType;
use Ramsey\Uuid\Uuid;

class LogRepository extends BaseRepository
{
    /**
     * Add new log to database
     * @param LogDto $dto
     * @return string $id - created record id
     */
    public function addLog(LogDto $dto): string
    {
        $value = json_encode($dto->value);
  
        // @todo find a way to send value as a parameter in execute 
        $sql = <<<"SQL"
            INSERT INTO log (`id`, `user_id`, `happened_at`, `action`, `table`, `record_id`, `value`) 
            VALUES (:id, :userId, :happenedAt, :action, :table, :recordId, '$value');
        SQL;
        
        $now = new DateTime();
        $id = Uuid::uuid4()->toString();
        
        $this->execute(
            $this->writeConn, 
            $sql,
            [
                'id'         => $id,
                'userId'     => $dto->userId->toString(),
                'happenedAt' => $now->format(BaseDto::FORMAT_DATE_TIME_DB),
                'action'     => $dto->action,
                'table'      => $dto->table,
                'recordId'   => $dto->recordId->toString()
            ]
        );
        
        return $id;
    }

    
    public function countUsersBookLogs(string $id): int
    {
        $sql = <<<'SQL'
            SELECT COUNT(l.id) as logNumber
            FROM log AS l
            WHERE l.record_id = :id
            AND l.`table` = :table
            GROUP BY l.record_id;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql,
            [
                'id'    => $id,
                'table' => UserBookManager::USER_BOOK_TABLE
            ]
        );
        
        return (int) $stmt->fetchColumn(0);
    }
    
    /**
     * Finds logs by user book id
     * @param string $id
     * @param array $params
     * @return array
     */
    public function findLogsByUserBookId(string $id, array $params = []): array
    {
        $params['limit'] = $params['limit'] ?? 10;
        $params['offset'] = !empty($params['page']) ? ($params['page'] - 1) * $params['limit'] : 0; 

        $sql = <<<'SQL'
            SELECT l.* 
            FROM log AS l 
            WHERE l.record_id = :id
            AND l.`table` = :table
            ORDER BY l.happened_at DESC
            LIMIT :offset, :limit;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql, 
            [
                'id'     => $id,
                'table'  => UserBookManager::USER_BOOK_TABLE,
                'offset' => (int) $params['offset'],
                'limit'  => (int) $params['limit']
            ],
            [
                'id'     => ParameterType::STRING,
                'table'  => ParameterType::STRING,
                'offset' => ParameterType::INTEGER,
                'limit'  => ParameterType::INTEGER
            ]
        );
        
        return $stmt->fetchAll();
    }
}

