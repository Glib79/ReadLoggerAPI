<?php
declare(strict_types=1);

namespace App\Repository;

use App\Support\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * Repository only for authentication: extends ServiceEntityRepository is needed because 
 * we have authentication base on UserInterface which needs User Entity and Repository
 */
class AuthRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * @var Connection
     */
    private $readConn;
    
    /**
     * AuthRepository constructor
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->readConn = $doctrine->getConnection('default');
    }
    
    /**
     * Method required by UserLoaderInterface for authentication
     * @param string $username
     * @throws InvalidArgumentException
     * @return User
     */
    public function loadUserByUsername(string $username): User
    {
        $sql = 'SELECT id, email, password, roles FROM user WHERE email = :email AND is_active = 1;';
        
        $stmt = $this->readConn->prepare($sql);
        $stmt->execute([
            'email' => $username,
        ]);
        $data = $stmt->fetch();
        
        if (!$data) {
            throw new InvalidArgumentException('No user!');
        }
        
        $user = new User($data['email'], Uuid::fromString($data['id']));
        $user->setPassword($data['password'])
            ->setRoles(json_decode($data['roles']));
            
        return $user;
    }
}

