<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\AuthenticationToken;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(DeviceController::class)]
#[CoversTrait(AuthenticationToken::class)]
class DeviceControllerTest extends TestCase
{
    use RequestHelper;

    public function testRegisterDispatchesEventAndThrowsWhenEventIsNotHandled(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<pushToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRegisteredEvent::class))
            ->willReturnArgument(0);

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DeviceRegisteredEvent was not handled. Please implement a listener for this event.');

        $controller = new DeviceController($eventDispatcher);
        $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    public function testRegisterReturnsHttpUnauthorized(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<pushToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRegisteredEvent::class))
            ->willReturnCallback(function (DeviceRegisteredEvent $event) {
                $event->notAuthorized();

                return $event;
            });

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRegisterReturnsHttpOk(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<pushToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRegisteredEvent::class))
            ->willReturnCallback(function (DeviceRegisteredEvent $event) {
                $event->alreadyRegistered();

                return $event;
            });

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testRegisterReturnsHttpCreated(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<pushToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRegisteredEvent::class))
            ->willReturnCallback(function (DeviceRegisteredEvent $event) {
                $event->deviceRegistered();

                return $event;
            });

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    public function testRegisterThrowsWhenEventWasNotHandledCorrectly(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<pushToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRegisteredEvent::class))
            ->willReturnCallback(function (DeviceRegisteredEvent $event) {
                $event->notModified();

                return $event;
            });

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DeviceRegisteredEvent was not handled correctly. Unexpected status was set.');

        $controller = new DeviceController($eventDispatcher);
        $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    public function testUnregisterDispatchesEventAndThrowsWhenEventIsNotHandled(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceUnregisteredEvent::class))
            ->willReturnArgument(0);

        $request = $this->createRequest($authenticationToken);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DeviceUnregisteredEvent was not handled. Please implement a listener for this event.');

        $controller = new DeviceController($eventDispatcher);
        $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    public function testUnregisterReturnsHttpUnauthorized(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceUnregisteredEvent::class))
            ->willReturnCallback(function (DeviceUnregisteredEvent $event) {
                $event->notAuthorized();

                return $event;
            });

        $request = $this->createRequest($authenticationToken);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testUnregisterReturnsHttpOk(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceUnregisteredEvent::class))
            ->willReturnCallback(function (DeviceUnregisteredEvent $event) {
                $event->deviceUnregistered();

                return $event;
            });

        $request = $this->createRequest($authenticationToken);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testUnregisterThrowsWhenEventWasNotHandledCorrectly(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceUnregisteredEvent::class))
            ->willReturnCallback(function (DeviceUnregisteredEvent $event) {
                $event->notModified();

                return $event;
            });

        $request = $this->createRequest($authenticationToken);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DeviceUnregisteredEvent was not handled correctly. Unexpected status was set.');

        $controller = new DeviceController($eventDispatcher);
        $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    #[DataProvider('malformedRegisterBodyProvider')]
    public function testRegisterReturnsBadRequestOnMalformedBody(string $body): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->never())->method('dispatch');

        $request = new Request([], [], [], [], [], [], $body);
        $request->headers->set('Authorization', 'ApplePass <authenticationToken>');

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, '<deviceLibraryIdentifier>', '<passTypeIdentifier>', '<serialNumber>');

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function malformedRegisterBodyProvider(): array
    {
        return [
            'not json' => ['this is not json'],
            'empty body' => [''],
            'missing pushToken' => ['{"foo": "bar"}'],
            'pushToken is not a string' => ['{"pushToken": ["array"]}'],
            'pushToken is empty' => ['{"pushToken": ""}'],
        ];
    }

    public function testGetSerialNumbersForwardsPassesUpdatedSinceToEvent(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnCallback(function (DeviceRequestUpdatedPassesEvent $event) {
                $this->assertNotNull($event->getPassesUpdatedSince());
                $this->assertSame('2019-12-04T10:40:01+00:00', $event->getPassesUpdatedSince()->format(DateTimeImmutable::ATOM));
                $event->notFound();

                return $event;
            });

        $request = new Request(['passesUpdatedSince' => '2019-12-04T10:40:01+00:00']);

        $controller = new DeviceController($eventDispatcher);
        $controller->getSerialNumbers($request, '<deviceLibraryIdentifier>', '<passTypeIdentifier>');
    }

    public function testGetSerialNumbersIgnoresUnparseablePassesUpdatedSince(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnCallback(function (DeviceRequestUpdatedPassesEvent $event) {
                $this->assertNull($event->getPassesUpdatedSince());
                $event->notFound();

                return $event;
            });

        $request = new Request(['passesUpdatedSince' => '<garbage>']);

        $controller = new DeviceController($eventDispatcher);
        $controller->getSerialNumbers($request, '<deviceLibraryIdentifier>', '<passTypeIdentifier>');
    }

    public function testGetSerialNumbersDispatchesEventAndThrowsWhenEventIsNotHandled(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnArgument(0);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DeviceRequestUpdatedPassesEvent was not handled. Please implement a listener for this event.');

        $controller = new DeviceController($eventDispatcher);
        $controller->getSerialNumbers(new Request(), $deviceLibraryIdentifier, $passTypeIdentifier);
    }

    public function testGetSerialNumbersReturnsHttpOk(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnCallback(function (DeviceRequestUpdatedPassesEvent $event) {
                $event->setSerialNumbers(['123', '456'], new DateTimeImmutable('2019-12-04T10:40:01+00:00'));

                return $event;
            });

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->getSerialNumbers(new Request(), $deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame('{"lastUpdated":"2019-12-04T10:40:01+00:00","serialNumbers":["123","456"]}', $response->getContent());
    }

    public function testGetSerialNumbersReturnsHttpNoContent(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnCallback(function (DeviceRequestUpdatedPassesEvent $event) {
                $event->notFound();

                return $event;
            });

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->getSerialNumbers(new Request(), $deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testGetSerialNumbersReturnsHttpNotModified(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnCallback(function (DeviceRequestUpdatedPassesEvent $event) {
                $event->notModified();

                return $event;
            });

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->getSerialNumbers(new Request(), $deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NOT_MODIFIED, $response->getStatusCode());
    }

    public function testGetSerialNumbersThrowsWhenEventWasNotHandledCorrectly(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->willReturnCallback(function (DeviceRequestUpdatedPassesEvent $event) {
                $event->notAuthorized();

                return $event;
            });

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DeviceRequestUpdatedPassesEvent was not handled correctly. Unexpected status was set.');

        $controller = new DeviceController($eventDispatcher);
        $controller->getSerialNumbers(new Request(), $deviceLibraryIdentifier, $passTypeIdentifier);
    }
}
