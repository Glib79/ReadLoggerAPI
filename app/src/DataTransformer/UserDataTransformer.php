<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\DTO\UserDto;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

class UserDataTransformer extends BaseDataTransformer
{
    /**
     * Transform Request to UserDto
     * @param Request $request
     * @return UserDto
     */
    public function transformInput(Request $request): UserDto
    {
        $dto = new UserDto($this->serializer, $this->validator);
        $data = json_decode($request->getContent(), true);
        
        $dto->email = $data['email'];
        $dto->password = $data['password'];
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $user
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $user, array $groups): array
    {
        $dto = new UserDto($this->serializer, $this->validator);
        $dto->id = Uuid::fromString($user['id']);
        $dto->email = $user['email'];
        $dto->roles = json_decode($user['roles']);
        $dto->createdAt = new DateTime($user['created_at']);
        $dto->modifiedAt = new DateTime($user['modified_at']);
        
        return $dto->normalize($groups);
    }
}