<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\BaseDto;
use App\DTO\LogDto;
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
}

