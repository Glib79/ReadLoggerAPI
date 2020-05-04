<?php
declare(strict_types=1);

namespace App\DataTransformer;

use ErrorException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseDataTransformer
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    
    /**
     * @var ValidatorInterface
     */
    protected $validator;
    
    /**
     * BaseDataTransformer constructor
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    
     /**
     * Transform array from database to array ready for output
     * @param array $data
     * @param array $groups
     * @return array
     */
    public function transformList(array $data, array $groups): array
    {
        $output = [];
        foreach ($data as $object) {
            $output[] = $this->transformOutput($object, $groups);
        }
        
        return $output;
    }
}