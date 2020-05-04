<?php
declare(strict_types=1);

namespace App\DataTransformer;

use DateTime;
use App\DataTransformer\AuthorDataTransformer;
use App\DTO\BaseDto;
use App\DTO\BookDto;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * @var AuthorDataTransformer 
     */
    private $authorDataTransformer;
    
    /**
     * BookDataTransformer constructor
     * @param AuthorDataTransformer $authorDataTransformer
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        AuthorDataTransformer $authorDataTransformer,
        SerializerInterface $serializer, 
        ValidatorInterface $validator
    ) {
        parent::__construct($serializer, $validator);
        
        $this->authorDataTransformer = $authorDataTransformer;
    }
    
    /**
     * Transform Request to BookDto
     * @param Request $request
     * @return BookDto
     */
    public function transformRequest(Request $request): BookDto
    {
        $data = json_decode($request->getContent(), true);
        
        return $this->transformArray($data);
    }
    
     /**
     * Transform Array to BookDto
     * @param array $data
     * @return BookDto
     */
    public function transformArray(array $data): BookDto
    {
        $dto = new BookDto($this->serializer, $this->validator);
        
        if (isset($data['id'])) {
            $dto->id = Uuid::fromString($data['id']);
        }
        if (isset($data['title'])) {
            $dto->title = $data['title'];
        }
        if (isset($data['subTitle'])) {
            $dto->subTitle = $data['subTitle'];
        }
        if (isset($data['size'])) {
            $dto->size = $data['size'];
        }
        
        if (isset($data['authors'])) {
            $authors = [];
            foreach ($data['authors'] as $author) {
                $authors[] = $this->authorDataTransformer->transformArray($author);
            }
            $dto->authors = $authors;
        }
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $book
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $book, array $groups): array
    {
        $dto = new BookDto($this->serializer, $this->validator);
        $dto->id = Uuid::fromString($book['id']);
        $dto->title = $book['title'];
        $dto->subTitle = $book['sub_title'];
        $dto->size = $book['size'];
        $dto->createdAt = new DateTime($book['created_at']);
        $dto->modifiedAt = new DateTime($book['modified_at']);
        
        if (isset($book['authors'])) {
            $dto->authors = $this->authorDataTransformer->transformList(
                $book['authors'], 
                [BaseDto::GROUP_LIST]
            );
        }
        
        return $dto->normalize($groups);
    }
}