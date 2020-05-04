<?php
declare(strict_types=1);

namespace App\DTO;

use App\Support\Error\ValidationException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

abstract class BaseDto
{
    //input
    public const GROUP_CREATE = 'create';
    public const GROUP_UPDATE = 'update';
    //output
    public const GROUP_AUTOSUGGEST = 'autosuggest';
    public const GROUP_LIST = 'list';
    public const GROUP_SINGLE = 'single';
    //formats
    public const FORMAT_DATE_TIME_DB = 'Y-m-d H:i:s';
    public const FORMAT_RATE_TIME_RESPONSE = 'Y-m-d H:i:s';
    
    /**
     * @var SerializerInterface
     */
    private $serializer;
    
    /**
     * @var ValidatorInterface
     */
    private $validator;
    
    /**
     * BaseDto constructor
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }
    
    /**
     * Validate Dto object
     * @param array $groups
     * @return bool
     */
    public function validate(array $groups): bool
    {
        $validationErrors = $this->validator->validate($this, null, $groups);
        
        if (count($validationErrors) > 0) {
            $errors = [];
            foreach ($validationErrors as $error) {
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }
            
            throw new ValidationException('Validation errors', $errors);
        }
        
        return true;
    }
    
    /**
     * Normalize Dto object to array
     * @param array $groups
     * @return array
     */
    public function normalize(array $groups): array
    {
        return $this->serializer->normalize(
            $this, 
            null, 
            [
                'groups' => $groups, 
                DateTimeNormalizer::FORMAT_KEY => self::FORMAT_RATE_TIME_RESPONSE
            ]
        );
    }
}