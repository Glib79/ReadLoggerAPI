<?php
declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\AuthorDataTransformer;
use App\DTO\BaseDto;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="author_api")
 */
class AuthorController extends BaseApiController
{
    /**
     * @var AuthorDataTransformer
     */
    private $authorDataTransformer;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * AuthorController constructor
     * @param AuthorDataTransformer $authorDataTransformer
     * @param AuthorRepository $authorRepository
     */
    public function __construct(
        AuthorDataTransformer $authorDataTransformer,
        AuthorRepository $authorRepository
    )
    {
        $this->authorDataTransformer = $authorDataTransformer;
        $this->authorRepository = $authorRepository;
    }
    
    /**
     * List authors for autosuggestion
     * @return JsonResponse
     * @Route("/authors/{query}", name="authors_query", methods={"GET"})
     */
    public function getAuthorsQuery(string $query): JsonResponse
    {
        $output = [];
        
        if ($query) {
            $output = $this->authorDataTransformer->transformList(
                $this->authorRepository->findAuthorsByQuery($query), 
                [BaseDto::GROUP_AUTOSUGGEST]
            );
        }
        
        return $this->response(
            Response::HTTP_OK, 
            'Authors found', 
            $output,
            ['count' => count($output)]
        );
    }
}

