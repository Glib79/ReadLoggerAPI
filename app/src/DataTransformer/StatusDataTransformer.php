<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\DTO\StatusDto;

class StatusDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * Transform Array to StatusDto
     * @param array $data
     * @return StatusDto
     */
    public function transformArray(array $data): StatusDto
    {
        $dto = new StatusDto($this->serializer, $this->validator);
        
        $dto->id = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->translationKey = $data['translationKey'] ?? null;
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $format
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $format, array $groups): array
    {
        $dto = new StatusDto($this->serializer, $this->validator);
        $dto->id = $format['id'];
        $dto->name = $format['name'] ?? null;
        $dto->translationKey = $format['translation_key'] ?? null;
        
        return $dto->normalize($groups);
    }
}
