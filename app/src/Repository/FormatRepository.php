<?php
declare(strict_types=1);

namespace App\Repository;

class FormatRepository extends BaseRepository
{
    /**
     * Collect all formats from database 
     * @return array
     */
    public function findFormats(): array
    {
        $sql = <<<'SQL'
            SELECT * FROM format;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql
        );
        
        return $stmt->fetchAll();
    }
}

