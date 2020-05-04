<?php
declare(strict_types=1);

namespace App\Repository;

use App\Support\Error\DatabaseException;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository
{
    /**
     * @var Connection
     */
    protected $readConn;
    
    /**
     * @var Connection
     */
    protected $writeConn;
    
    /**
     * BaseRepository constructor
     * @param Connection $connection
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->readConn = $doctrine->getConnection('default');
        $this->writeConn = $doctrine->getConnection('write');
    }
    
    /**
     * Prepare sql query based on given connection and executes it using given params
     * @param Connection $conn
     * @param string $sql
     * @param array $params
     * @param array $types
     * @throws DatabaseException
     * @return Statement
     */
    protected function execute(Connection $conn, string $sql, array $params = [], array $types = []): Statement
    {
        $stmt = $conn->prepare($sql);
        if ($types) {
            foreach ($params as $key => $param) {
                $stmt->bindValue($key, $param, $types[$key]);
            }
            $stmt->execute();
        } else {
            $stmt->execute($params);
        }
        
        if ($stmt->errorCode() !== '00000') {
            throw new DatabaseException(json_encode($stmt->errorInfo()));
        }
        
        return $stmt;
    }
}