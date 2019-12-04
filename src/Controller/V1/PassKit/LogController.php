<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/log")
 */
class LogController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function log(Request $request): JsonResponse
    {
        $this->logger->info('Apple passbook Log request', json_decode($request->getContent(), true));

        return new JsonResponse();
    }
}
