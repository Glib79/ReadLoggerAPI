<?php
declare(strict_types=1);

namespace App\Repository;

class LanguageRepository extends BaseRepository
{
    /**
     * Collect all languages from database 
     * @return array
     */
    public function findLanguages(): array
    {
        $sql = <<<'SQL'
            SELECT * FROM language;
        SQL;
        
        $stmt = $this->execute(
            $this->readConn, 
            $sql
        );
        
        return $stmt->fetchAll();
    }
}

