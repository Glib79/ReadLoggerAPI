<?php
declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\LogDataTransformer;
use App\DTO\BaseDto;
use App\Repository\LogRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="author_api")
 */
class LogController extends BaseApiController
{
    private const DEFAULT_LIMIT = 10;
    
    /**
     * @var LogDataTransformer
     */
    private $logDataTransformer;

    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * LogController constructor
     * @param LogDataTransformer $logDataTransformer
     * @param logRepository $logRepository
     */
    public function __construct(
        LogDataTransformer $logDataTransformer,
        LogRepository $logRepository
    )
    {
        $this->logDataTransformer = $logDataTransformer;
        $this->logRepository = $logRepository;
    }
    
    /**
     * User book history
     * @param Request $request
     * @param string $id user book id
     * @return JsonResponse
     * @Route("/log/{id}", name="log_user_book", methods={"GET"})
     */
    public function getLogByUserBook(Request $request, string $id): JsonResponse
    {
        $limit = $this::DEFAULT_LIMIT;
        $page = $request->query->getInt('page', 1);

        $output = [];
        
        if ($id) {
            $output = $this->logDataTransformer->transformList(
                $this->logRepository->findLogsByUserBookId(
                    $id,
                    [
                        'limit'  => $limit,
                        'page'   => $page
                    ]
                ), 
                [BaseDto::GROUP_LIST]
            );
        }
        
        return $this->response(
            Response::HTTP_OK, 
            'History found', 
            $output,
            [
                'count' => count($output),
                'page'  => $page,
                'pages' => ceil($this->logRepository->countUsersBookLogs($id) / $limit)
            ]
        );
    }
}

