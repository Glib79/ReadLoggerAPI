<?php
declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\FormatDataTransformer;
use App\DataTransformer\LanguageDataTransformer;
use App\DataTransformer\StatusDataTransformer;
use App\DTO\BaseDto;
use App\Repository\FormatRepository;
use App\Repository\LanguageRepository;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="support_api")
 */
class SupportController extends BaseApiController
{
    /**
     * @var FormatDataTransformer
     */
    private $formatDataTransformer;

    /**
     * @var FormatRepository
     */
    private $formatRepository;

    /**
     * @var LanguageDataTransformer
     */
    private $languageDataTransformer;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var StatusDataTransformer
     */
    private $statusDataTransformer;

    /**
     * @var StatusRepository
     */
    private $statusRepository;

    /**
     * SupportController constructor
     * @param FormatDataTransformer $formatDataTransformer
     * @param FormatRepository $formatRepository
     * @param LanguageDataTransformer $languageDataTransformer
     * @param LanguageRepository $languageRepository
     * @param StatusDataTransformer $statusDataTransformer
     * @param StatusRepository $statusRepository
     */
    public function __construct(
        FormatDataTransformer $formatDataTransformer,
        FormatRepository $formatRepository,
        LanguageDataTransformer $languageDataTransformer,
        LanguageRepository $languageRepository,
        StatusDataTransformer $statusDataTransformer,
        StatusRepository $statusRepository
    )
    {
        $this->formatDataTransformer = $formatDataTransformer;
        $this->formatRepository = $formatRepository;
        $this->languageDataTransformer = $languageDataTransformer;
        $this->languageRepository = $languageRepository;
        $this->statusDataTransformer = $statusDataTransformer;
        $this->statusRepository = $statusRepository;
    }
    
    /**
     * Lists from dictionary tables like format, language, status 
     * @return JsonResponse
     * @Route("/support/{resources}", name="support_resources", methods={"GET"})
     */
    public function getSupportResources(string $resources): JsonResponse
    {
        $resources = explode('-', $resources);
        $output = [];
        $meta = [];
        
        if ($resources) {
            foreach ($resources as $resource) {
                switch ($resource) {
                    case 'format':
                        $output[$resource] = $this->formatDataTransformer->transformList(
                            $this->formatRepository->findFormats(), 
                            [BaseDto::GROUP_LIST]
                        );
                        $meta['counts'][$resource] = count($output[$resource]);
                        break;
                    case 'language':
                        $output[$resource] = $this->languageDataTransformer->transformList(
                            $this->languageRepository->findLanguages(), 
                            [BaseDto::GROUP_LIST]
                        );
                        $meta['counts'][$resource] = count($output[$resource]);
                        break;
                    case 'status':
                        $output[$resource] = $this->statusDataTransformer->transformList(
                            $this->statusRepository->findStatuses(), 
                            [BaseDto::GROUP_LIST]
                        );
                        $meta['counts'][$resource] = count($output[$resource]);
                        break;
                }
            }
        }
        
        return $this->response(
            Response::HTTP_OK, 
            'Resources found', 
            $output,
            $meta
        );
    }
}
