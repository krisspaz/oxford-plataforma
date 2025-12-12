<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request   = $event->getRequest();

        // Only format for API requests
        if (strpos($request->getPathInfo(), '/api') !== 0) {
            return;
        }

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        
        $errorData = [
            'status' => 'error',
            'code'   => $statusCode,
            'message' => $exception->getMessage(),
        ];

        // Add debug info in dev mode (you might want to control this via ENV)
        if ($_ENV['APP_ENV'] === 'dev') {
            $errorData['trace'] = $exception->getTraceAsString();
        }

        $this->logger->error(sprintf('API Error: %s', $exception->getMessage()), ['exception' => $exception]);

        $response = new JsonResponse($errorData, $statusCode);
        $event->setResponse($response);
    }
}
