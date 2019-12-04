<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController
 */
class LogControllerTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController::log
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController::__construct
     */
    public function testLog(): void
    {
        $errorArray = ['error' => 'message'];

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with('Apple passbook Log request', $errorArray);

        $request = new Request([], [], [], [], [], [], json_encode($errorArray));

        $controller = new LogController($logger);
        $response = $controller->log($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('{}', $response->getContent());

    }
}