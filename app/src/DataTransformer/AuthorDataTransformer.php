<?php
declare(strict_types=1);

namespace App\DataTransformer;

use DateTime;
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
        
        if (isset($data['id'])) {
            $dto->id = Uuid::fromString($data['id']);
        }
        if (isset($data['firstName'])) {
            $dto->firstName = $data['firstName'];
        }
        if (isset($data['lastName'])) {
            $dto->lastName = $data['lastName'];
        }
        
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
        $dto->createdAt = !empty($author['created_at']) ? new DateTime($author['created_at']) : null;
        $dto->modifiedAt = !empty($author['modified_at']) ? new DateTime($author['modified_at']) : null;
        
        return $dto->normalize($groups);
    }
}