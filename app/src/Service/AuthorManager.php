<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\AuthorDto;
use App\Repository\AuthorRepository;

class AuthorManager
{
    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    
    /**
     * AuthorManager constructor
     * @param AuthorRepository $authorRepository
     */
    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * Creates author from DTO
     * @param AuthorDto $dto
     * @return string $id - created record id
     */
    public function createAuthor(AuthorDto $dto): string
    {
        return $this->authorRepository->addAuthor($dto);
    }
}