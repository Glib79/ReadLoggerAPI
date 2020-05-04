<?php
declare(strict_types=1);

namespace App\Repository;

class StatusRepository extends BaseRepository
{
    /**
     * Collect all statuses from database 
     * @return array
     */
    public function findStatuses(): array
    {
        $sql = <<<'SQL'
            SELECT * FROM status;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql
        );
        
        return $stmt->fetchAll();
    }
}

