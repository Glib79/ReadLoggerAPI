<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\LogDto;
use App\Repository\LogRepository;

class LogManager
{
    /**
     * @var LogRepository
     */
    private $logRepository;
    
    /**
     * LogManager constructor
     * @param LogRepository $logRepository
     */
    public function __construct(
        LogRepository $logRepository
    )
    {
        $this->logRepository = $logRepository;
    }

    /**
     * Add log
     * @param LogDto $dto
     * @return string $id - created record id
     */
    public function addLog(LogDto $dto): string
    {
        return $this->logRepository->addLog($dto);
    }
}