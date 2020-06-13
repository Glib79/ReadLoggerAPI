<?php
declare(strict_types=1);

namespace App\Repository;

use App\DTO\UserDto;
use Ramsey\Uuid\Uuid;

class UserRepository extends BaseRepository
{
    /**
     * Confirm user email in database
     * @param string $id
     * @return void
     */
    public function confirmEmail (string $id): void
    {
        $sql = 'UPDATE user SET is_confirmed = 1, token = "" 
                WHERE id = :id;';
        
        $this->execute(
            $this->readConn, 
            $sql,
            ['id' => $id]
        );
    }
    
    /**
     * Add new user to database
     * @param UserDto $user
     * @return string $id - created record id
     */
    public function createUser(UserDto $user): string
    {
        $sql = 'INSERT INTO user (id, email, password, roles, is_active, is_confirmed, token, language) 
                VALUES (:id, :email, :password, :roles, :isActive, :isConfirmed, :token, :language);';
        
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
                'token'       => $user->token,
                'language'    => $user->language
            ]
        );
        
        return $id;
    }
    
    /**
     * Finds user by email and token
     * @param string $email
     * @param string $token
     * @return array
     */
    public function findUserByEmailAndToken(string $email, string $token): array
    {
        $sql = 'SELECT * FROM user WHERE email = :email AND token = :token;';

        $stmt = $this->execute(
            $this->readConn, 
            $sql, 
            [
                'email' => $email,
                'token' => $token
            ]
        );
        
        return $stmt->fetch() ?: [];
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

    /**
     * Set new token for user
     * @param string $id
     * @param string $token
     * @return void
     */
    public function setToken (string $id, string $token): void
    {
        $sql = 'UPDATE user SET token = :token 
                WHERE id = :id;';
        
        $this->execute(
            $this->readConn, 
            $sql,
            [
                'id'    => $id,
                'token' => $token
            ]
        );
    }

}

