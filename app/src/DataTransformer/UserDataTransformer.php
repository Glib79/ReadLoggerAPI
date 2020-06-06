<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\DTO\UserDto;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

class UserDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * Transform Request to UserDto
     * @param Request $request
     * @return UserDto
     */
    public function transformRequest(Request $request): UserDto
    {
        $dto = new UserDto($this->serializer, $this->validator);
        $data = json_decode($request->getContent(), true);
        
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        
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
        $dto->email = $user['email'] ?? null;
        $dto->roles = !empty($user['roles']) ? json_decode($user['roles']) : [];
        $dto->isActive = isset($user['is_active']) ? (bool) $user['is_active'] : null;
        $dto->isConfirmed = isset($user['is_confirmed']) ? (bool) $user['is_confirmed'] : null;
        $dto->token = $user['token'] ?? null;
        
        return $dto->normalize($groups);
    }
}