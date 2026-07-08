<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(LogController::class)]
class LogControllerTest extends TestCase
{
    public function testLog(): void
    {
        $logs = ['Device reported an error.', 'Second message.'];

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with('Apple PassKit log request', ['logs' => $logs]);

        $request = new Request([], [], [], [], [], [], (string) json_encode(['logs' => $logs]));

        $controller = new LogController($logger);
        $response = $controller->log($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame('{}', $response->getContent());
    }

    public function testLogTruncatesOversizedEntriesAndDropsNonStrings(): void
    {
        $longMessage = str_repeat('a', 5000);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            'Apple PassKit log request',
            ['logs' => [substr($longMessage, 0, 4096), 'valid']]
        );

        $request = new Request([], [], [], [], [], [], (string) json_encode(['logs' => [$longMessage, ['nested' => 'array'], 'valid']]));

        $controller = new LogController($logger);
        $response = $controller->log($request);

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testLogCapsTheNumberOfEntries(): void
    {
        $logs = array_fill(0, 60, 'message');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')->with(
            'Apple PassKit log request',
            ['logs' => array_fill(0, 50, 'message')]
        );

        $request = new Request([], [], [], [], [], [], (string) json_encode(['logs' => $logs]));

        $controller = new LogController($logger);
        $controller->log($request);
    }

    #[DataProvider('malformedBodyProvider')]
    public function testLogReturnsBadRequestOnMalformedBody(string $body): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $request = new Request([], [], [], [], [], [], $body);

        $controller = new LogController($logger);
        $response = $controller->log($request);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function malformedBodyProvider(): array
    {
        return [
            'not json' => ['this is not json'],
            'empty body' => [''],
            'scalar json' => ['42'],
            'missing logs key' => ['{"error": "message"}'],
            'logs is not an array' => ['{"logs": "message"}'],
        ];
    }
}
