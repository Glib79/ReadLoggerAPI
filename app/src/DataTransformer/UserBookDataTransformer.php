<?php
declare(strict_types=1);

namespace App\DataTransformer;

use DateTime;
use App\DataTransformer\BookDataTransformer;
use App\DataTransformer\FormatDataTransformer;
use App\DataTransformer\LanguageDataTransformer;
use App\DataTransformer\StatusDataTransformer;
use App\DTO\UserBookDto;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserBookDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * @var BookDataTransformer 
     */
    private $bookDataTransformer;

    /**
     * @var FormatDataTransformer 
     */
    private $formatDataTransformer;

    /**
     * @var LanguageDataTransformer 
     */
    private $languageDataTransformer;

    /**
     * @var StatusDataTransformer 
     */
    private $statusDataTransformer;

    /**
     * BookDataTransformer constructor
     * @param BookDataTransformer $bookDataTransformer
     * @param FormatDataTransformer $formatDataTransformer
     * @param LanguageDataTransformer $languageDataTransformer
     * @param StatusDataTransformer $statusDataTransformer
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        BookDataTransformer $bookDataTransformer,
        FormatDataTransformer $formatDataTransformer,
        LanguageDataTransformer $languageDataTransformer,
        StatusDataTransformer $statusDataTransformer,
        SerializerInterface $serializer, 
        ValidatorInterface $validator
    ) {
        parent::__construct($serializer, $validator);
        
        $this->bookDataTransformer = $bookDataTransformer;
        $this->formatDataTransformer = $formatDataTransformer;
        $this->languageDataTransformer = $languageDataTransformer;
        $this->statusDataTransformer = $statusDataTransformer;
    }
    
    /**
     * Transform Request to UserBookDto
     * @param Request $request
     * @return UserBookDto
     */
    public function transformRequest(Request $request): UserBookDto
    {
        $dto = new UserBookDto($this->serializer, $this->validator);
        $data = json_decode($request->getContent(), true);
        
        $dto->book = $this->bookDataTransformer->transformArray($data['book']);
        $dto->status = $this->statusDataTransformer->transformArray($data['status']);
        $dto->format = $this->formatDataTransformer->transformArray($data['format']);
        $dto->language= $this->languageDataTransformer->transformArray($data['language']);
        
        if (isset($data['startDate'])) {
            $dto->startDate = new DateTime($data['startDate']);
        }
        if (isset($data['endDate'])) {
            $dto->endDate = new DateTime($data['endDate']);
        }
        if (isset($data['rating'])) {
            $dto->rating = $data['rating'];
        }
        if (isset($data['notes'])) {
            $dto->notes = $data['notes'];
        }
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $userBook
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $userBook, array $groups): array
    {
        $dto = new UserBookDto($this->serializer, $this->validator);
        $dto->id = Uuid::fromString($userBook['id']);
        $dto->book = $this->bookDataTransformer->transformOutput(
            $userBook['book'], 
            $groups
        );
        $dto->startDate = !empty($userBook['start_date']) ? new DateTime($userBook['start_date']) : null;
        $dto->endDate = !empty($userBook['end_date']) ? new DateTime($userBook['end_date']) : null;
        $dto->status = $this->statusDataTransformer->transformOutput(
            $userBook['status'], 
            $groups
        );
        $dto->format = $this->formatDataTransformer->transformOutput(
            $userBook['format'], 
            $groups
        );
        $dto->rating = $userBook['rating'] ?? null;
        $dto->language = $this->languageDataTransformer->transformOutput(
            $userBook['language'], 
            $groups
        );
        $dto->notes = $userBook['notes'] ?? null;
        $dto->createdAt = !empty($userBook['created_at']) ? new DateTime($userBook['created_at']) : null;
        $dto->modifiedAt = !empty($userBook['modified_at']) ? new DateTime($userBook['modified_at']) : null;
        
        return $dto->normalize($groups);
    }
}