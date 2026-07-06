<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LogController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function log(Request $request): JsonResponse
    {
        $this->logger->info('Apple passbook Log request', json_decode($request->getContent(), true));

        return new JsonResponse();
    }
}
