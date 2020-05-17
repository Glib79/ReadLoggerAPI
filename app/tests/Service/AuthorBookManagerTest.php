<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\AuthorBookManager;
use App\Repository\AuthorBookRepository;
use App\Tests\BaseTestCase;

class AuthorBookManagerTest extends BaseTestCase
{
    /**
     * SCENARIO: receiving list of authorIds
     * EXPECTED: return list of authors
     */
    public function testFindAuthorsByBooks()
    {
        $authorBookRepository = $this->createMock(AuthorBookRepository::class);
        $authorBookRepository->expects($this->once())
            ->method('findAuthorsByBooks')
            ->with(['bookId_string'])
            ->willReturn([
                [
                    'id'      => 'authorId_string',
                    'book_id' => 'bookId_string'
                ]
            ]); 
        
        $authorBookManager = new AuthorBookManager($authorBookRepository);
        
        $result = $authorBookManager->findAuthorsByBooks(['bookId_string']);
        
        $this->assertEqualsCanonicalizing($result, [
            'bookId_string' => [
                [
                    'id'      => 'authorId_string',
                    'book_id' => 'bookId_string'
                ]
            ]
        ]);
    }
}