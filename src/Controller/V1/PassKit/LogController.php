<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController
{
    private const MAX_LOG_ENTRIES = 50;
    private const MAX_LOG_ENTRY_LENGTH = 4096;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function log(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!is_array($content) || !isset($content['logs']) || !is_array($content['logs'])) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $logs = [];
        foreach (array_slice($content['logs'], 0, self::MAX_LOG_ENTRIES) as $log) {
            if (is_string($log)) {
                $logs[] = substr($log, 0, self::MAX_LOG_ENTRY_LENGTH);
            }
        }

        $this->logger->info('Apple PassKit log request', ['logs' => $logs]);

        return new JsonResponse();
    }
}
