<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\UserDto;
use Ramsey\Uuid\Uuid;

class UserRepository extends BaseRepository
{
    /**
     * Add new user to database
     * @param UserDto $user
     * @return string $id - created record id
     */
    public function createUser(UserDto $user): string
    {
        $sql = 'INSERT INTO user (id, email, password, roles, is_active, is_confirmed, token) 
                VALUES (:id, :email, :password, :roles, :isActive, :isConfirmed, :token);';
        
        $id = Uuid::uuid4()->toString();
        
        $this->execute(
            $this->readConn, 
            $sql,
            [
                'id'          => $id,
                'email'       => $user->email,
                'password'    => $user->password,
                'roles'       => json_encode($user->roles),
                'isActive'    => (int) $user->isActive,
                'isConfirmed' => (int) $user->isConfirmed,
                'token'       => $user->token
            ]
        );
        
        return $id;
    }
    
    /**
     * Get single user by id
     * @param string $id
     * @return array
     */
    public function getUserById(string $id): array
    {
        $sql = 'SELECT * FROM user WHERE id = :id;';

        $stmt = $this->execute($this->readConn, $sql, ['id' => $id]);
        
        return $stmt->fetch() ?: [];
    }
}

