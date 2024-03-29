<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\DTO\AuthorDto;
use Ramsey\Uuid\Uuid;

class AuthorDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * Transform Array to AuthorDto
     * @param array $data
     * @return AuthorDto
     */
    public function transformArray(array $data): AuthorDto
    {
        $dto = new AuthorDto($this->serializer, $this->validator);
        
        $dto->id = !empty($data['id']) ? Uuid::fromString($data['id']) : null;
        $dto->firstName = $data['firstName'] ?? null;
        $dto->lastName = $data['lastName'] ?? null;
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $author
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $author, array $groups): array
    {
        $dto = new AuthorDto($this->serializer, $this->validator);
        $dto->id = Uuid::fromString($author['id']);
        $dto->firstName = $author['first_name'] ?? null;
        $dto->lastName = $author['last_name'] ?? null;
        
        return $dto->normalize($groups);
    }
}