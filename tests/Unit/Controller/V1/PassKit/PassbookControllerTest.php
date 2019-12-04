<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbook\Passbook;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PassbookControllerTest extends TestCase
{
    use RequestHelper;

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::getUpdatedPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::__construct
     */
    public function testRegisterDispatchesEventAndThrowsWhenEventIsNotHandled(): void
    {
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $compiler = $this->createMock(Compiler::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RetrieveUpdatedPassbookEvent::class))
            ->willReturnArgument(0);

        $request = $this->createRequest($authenticationToken);

        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('RetrieveUpdatedPassbookEvent was not handled. Please implement a listener for this event.');

        $controller = new PassbookController($compiler, $eventDispatcher);
        $controller->getUpdatedPassbook($request, $passTypeIdentifier, $serialNumber);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::getUpdatedPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::__construct
     */
    public function testRegisterReturnsHttpUnauthorized(): void
    {
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $compiler = $this->createMock(Compiler::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RetrieveUpdatedPassbookEvent::class))
            ->will($this->returnCallback(function(RetrieveUpdatedPassbookEvent $event) {
                $event->notAuthorized();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $controller = new PassbookController($compiler, $eventDispatcher);
        $response = $controller->getUpdatedPassbook($request, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::getUpdatedPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::__construct
     */
    public function testRegisterReturnsHttpNotModified(): void
    {
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $compiler = $this->createMock(Compiler::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RetrieveUpdatedPassbookEvent::class))
            ->will($this->returnCallback(function(RetrieveUpdatedPassbookEvent $event) {
                $event->notModified();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $controller = new PassbookController($compiler, $eventDispatcher);
        $response = $controller->getUpdatedPassbook($request, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NOT_MODIFIED, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::getUpdatedPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::__construct
     */
    public function testRegisterThrowsWhenEventWasNotHandledCorrectly(): void
    {
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $compiler = $this->createMock(Compiler::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RetrieveUpdatedPassbookEvent::class))
            ->will($this->returnCallback(function(RetrieveUpdatedPassbookEvent $event) {
                $event->notFound();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('RetrieveUpdatedPassbookEvent was not handled correctly. Unexpected status was set.');

        $controller = new PassbookController($compiler, $eventDispatcher);
        $controller->getUpdatedPassbook($request, $passTypeIdentifier, $serialNumber);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::getUpdatedPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController::__construct
     */
    public function testRegisterReturnsPassbook(): void
    {
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $passbook = $this->createMock(Passbook::class);
        $lastModified = new DateTimeImmutable('2019-12-04 14:40:01', new \DateTimeZone('Europe/Amsterdam'));

        $compiler = $this->createMock(Compiler::class);
        $compiler->expects($this->once())->method('compile')->with($passbook)->willReturn('<passbookCompiledData>');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RetrieveUpdatedPassbookEvent::class))
            ->will($this->returnCallback(function(RetrieveUpdatedPassbookEvent $event) use ($passbook, $lastModified) {
                $event->setPassbook($passbook, $lastModified);

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $controller = new PassbookController($compiler, $eventDispatcher);
        $response = $controller->getUpdatedPassbook($request, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $this->assertHeader('File Transfer', 'Content-Description', $response);
        $this->assertHeader('File Transfer', 'Content-Description', $response);
        $this->assertHeader('application/vnd.apple.pkpass', 'Content-Type', $response);
        $this->assertHeader('filename="pass.pkpass"', 'Content-Disposition', $response);
        $this->assertHeader('Wed, 04 Dec 2019 13:40:01 GMT', 'Last-Modified', $response);

        $this->assertSame('<passbookCompiledData>', $response->getContent());
    }

    private function assertHeader(string $expected, string $key, Response $response): void
    {
        $this->assertSame($expected, $response->headers->get($key));
    }
}