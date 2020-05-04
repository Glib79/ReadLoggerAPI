<?php
declare(strict_types=1);

namespace App\DataTransformer;

interface DataTransformerInterface
{
    /**
     * Transform array from database to array ready for output
     * @param array $data
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $data, array $groups): array;    
}
