<?php
declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\BookDataTransformer;
use App\DTO\BaseDto;
use App\Service\BookManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="book_api")
 */
class BookController extends BaseApiController
{
    /**
     * @var BookDataTransformer
     */
    private $bookDataTransformer;

    /**
     * @var BookManager
     */
    private $bookManager;

    /**
     * BookController constructor
     * @param BookDataTransformer $bookDataTransformer
     * @param BookManager $bookManager
     */
    public function __construct(
        BookDataTransformer $bookDataTransformer,
        BookManager $bookManager
    )
    {
        $this->bookDataTransformer = $bookDataTransformer;
        $this->bookManager = $bookManager;
    }
    
    /**
     * List books for autosuggestion
     * @return JsonResponse
     * @Route("/books/{query}", name="books_query", methods={"GET"})
     */
    public function getBooksQuery(string $query): JsonResponse
    {
        $output = [];
        
        if ($query) {
            $output = $this->bookDataTransformer->transformList(
                $this->bookManager->findBooksByQuery($query), 
                [BaseDto::GROUP_AUTOSUGGEST]
            );
        }
        
        return $this->response(
            Response::HTTP_OK, 
            'Books found', 
            $output,
            ['count' => count($output)]
        );
    }
}

