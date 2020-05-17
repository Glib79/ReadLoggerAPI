<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\AuthorDto;
use App\Repository\AuthorRepository;
use App\Service\AuthorManager;
use App\Tests\BaseTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving AuthorDto object
     * EXPECTED: save to database new Author 
     */
    public function testCreateAuthor()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        
        $authorDto = new AuthorDto($serializer, $validator);
        $authorDto->firstName = 'firstName_string';
        $authorDto->lastName = 'lastName_string';
        
        $authorRepository = $this->createMock(AuthorRepository::class);
        $authorRepository->expects($this->once())
            ->method('addAuthor')
            ->with($authorDto)
            ->willReturn('id_string'); 
        
        $authorManager = new AuthorManager($authorRepository);
        
        $result = $authorManager->createAuthor($authorDto);
        
        $this->assertSame('id_string', $result);
    }
}