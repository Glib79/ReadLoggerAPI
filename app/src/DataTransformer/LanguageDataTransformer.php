<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\DTO\LanguageDto;

class LanguageDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * Transform Array to LanguageDto
     * @param array $data
     * @return LanguageDto
     */
    public function transformArray(array $data): LanguageDto
    {
        $dto = new LanguageDto($this->serializer, $this->validator);
        
        if (isset($data['id'])) {
            $dto->id = $data['id'];
        }
        if (isset($data['symbol'])) {
            $dto->symbol = $data['symbol'];
        }
        if (isset($data['translationKey'])) {
            $dto->translationKey = $data['translationKey'];
        }
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $language
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $language, array $groups): array
    {
        $dto = new LanguageDto($this->serializer, $this->validator);
        $dto->id = $language['id'];
        $dto->symbol = $language['symbol'] ?? null;
        $dto->translationKey = $language['translation_key'] ?? null;
        
        return $dto->normalize($groups);
    }
}