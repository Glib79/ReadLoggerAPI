<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Controller\BaseApiController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionListener
{
    /**
     * @var BaseApiController
     */
    private $controller;
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * ExceptionListener constructor
     * @param BaseApiController $controller
     * @param LoggerInterface $logger
     */
    public function __construct(BaseApiController $controller, LoggerInterface $logger)
    {
        $this->controller = $controller;
        $this->logger = $logger;
    }
    
    /**
     * Listener
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        $this->logger->error(sprintf('%s occurred with messsage: %s', get_class($exception), $exception->getMessage()));
        
        $response = $this->createResponse($exception);
        $event->setResponse($response);
    }
    
    /**
     * Creates the JsonResponse from any Exception
     * @param Exception $exception
     * @return JsonResponse
     */
    private function createResponse(Throwable $exception): JsonResponse
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        
        return $this->controller->response($statusCode, 'Something went wrong!');
    }
}