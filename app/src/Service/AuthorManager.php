<?php
declare(strict_types=1);

namespace App\Service;

use App\DataTransformer\LogDataTransformer;
use App\DTO\AuthorDto;
use App\DTO\LogDto;
use App\Repository\AuthorRepository;
use App\Service\LogManager;
use Ramsey\Uuid\Uuid;

class AuthorManager
{
    private const AUTHOR_TABLE = 'author';
    
    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    
    /**
     * @var LogDataTransformer
     */
    private $logDataTransformer;
 
    /**
     * @var LogManager
     */
    private $logManager;    
    
    /**
     * AuthorManager constructor
     * @param AuthorRepository $authorRepository
     * @param LogDataTransformer $logDataTransformer
     * @param LogManager $logManager
     */
    public function __construct(
        AuthorRepository $authorRepository,
        LogDataTransformer $logDataTransformer,
        LogManager $logManager
    )
    {
        $this->authorRepository = $authorRepository;
        $this->logDataTransformer = $logDataTransformer;
        $this->logManager = $logManager;
    }

    /**
     * Creates author from DTO
     * @param AuthorDto $dto
     * @param Uuid $userId
     * @return string $id - created record id
     */
    public function createAuthor(AuthorDto $dto, Uuid $userId): string
    {
        $id = $this->authorRepository->addAuthor($dto);
        $dto->id = Uuid::fromString($id);
        
        $logDto = $this->logDataTransformer->prepareLog(
            $userId, 
            LogDto::ACTION_CREATE, 
            self::AUTHOR_TABLE, 
            $dto
        );
        
        $this->logManager->addLog($logDto);
        
        return $id;
    }
}