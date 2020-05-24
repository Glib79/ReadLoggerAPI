<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\LogDto;
use App\Service\LogManager;
use App\Repository\LogRepository;
use App\Tests\BaseTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LogManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving LogDto object with not empty value
     * EXPECTED: save it to database
     */
    public function testAddLogWithValue()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->value = ['key' => 'value'];
        
        $logRepository = $this->createMock(LogRepository::class);
        $logRepository->expects($this->once())
            ->method('addLog')
            ->with($logDto)
            ->willReturn('id_string'); 
        
        $logManager = new LogManager($logRepository);
        
        $result = $logManager->addLog($logDto);
        
        $this->assertSame('id_string', $result);
    }
    
    /**
     * SCENARIO: receiving LogDto object with empty value
     * EXPECTED: do nothing
     */
    public function testAddLogWithoutValue()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $logDto = new LogDto($serializer, $validator);
        $logDto->value = [];
        
        $logRepository = $this->createMock(LogRepository::class);
        $logRepository->expects($this->never())
            ->method($this->anything());
        
        $logManager = new LogManager($logRepository);
        
        $result = $logManager->addLog($logDto);
        
        $this->assertSame('', $result);
    }
}