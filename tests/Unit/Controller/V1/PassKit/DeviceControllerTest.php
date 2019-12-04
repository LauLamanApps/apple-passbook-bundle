<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Controller\V1\PassKit;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController
 */
class DeviceControllerTest extends TestCase
{
    use RequestHelper;

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
        $this->expectDeprecationMessage('DeviceRegisteredEvent was not handled. Please implement a listener for this event.');

        $controller = new DeviceController($eventDispatcher);
        $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceRegisteredEvent $event) {
                $event->notAuthorized();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceRegisteredEvent $event) {
                $event->alreadyRegistered();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceRegisteredEvent $event) {
                $event->deviceRegistered();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceRegisteredEvent $event) {
                $event->notModified();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken, ['pushToken' => $pushToken]);

        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('DeviceRegisteredEvent was not handled correctly. Unexpected status was set.');

        $controller = new DeviceController($eventDispatcher);
        $controller->register($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::unregister
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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

        $request = new Request();
        $request->headers->add(['Authorization: ApplePass '. $authenticationToken]);

        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('DeviceUnregisteredEvent was not handled. Please implement a listener for this event.');

        $controller = new DeviceController($eventDispatcher);
        $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::unregister
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceUnregisteredEvent $event) {
                $event->notAuthorized();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::unregister
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceUnregisteredEvent $event) {
                $event->deviceUnregistered();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::unregister
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
            ->will($this->returnCallback(function(DeviceUnregisteredEvent $event) {
                $event->notModified();

                return $event;
            }));

        $request = $this->createRequest($authenticationToken);

        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('DeviceUnregisteredEvent was not handled correctly. Unexpected status was set.');

        $controller = new DeviceController($eventDispatcher);
        $controller->unregister($request, $deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
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
        $this->expectDeprecationMessage('DeviceRequestUpdatedPassesEvent was not handled. Please implement a listener for this event.');

        $controller = new DeviceController($eventDispatcher);
        $controller->getSerialNumbers($deviceLibraryIdentifier, $passTypeIdentifier);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
    public function testGetSerialNumbersReturnsHttpOk(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->will($this->returnCallback(function(DeviceRequestUpdatedPassesEvent $event) {
                $event->setSerialNumbers(['123', '456'], new DateTimeImmutable('2019-12-04T10:40:01+00:00'));

                return $event;
            }));

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->getSerialNumbers($deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame('{"lastUpdated":"2019-12-04T10:40:01+00:00","serialNumbers":["123","456"]}', $response->getContent());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
    public function testGetSerialNumbersReturnsHttpNoContent(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->will($this->returnCallback(function(DeviceRequestUpdatedPassesEvent $event) {
                $event->notFound();

                return $event;
            }));

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->getSerialNumbers($deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
    public function testGetSerialNumbersReturnsHttpNotModified(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->will($this->returnCallback(function(DeviceRequestUpdatedPassesEvent $event) {
                $event->notModified();

                return $event;
            }));

        $controller = new DeviceController($eventDispatcher);
        $response = $controller->getSerialNumbers($deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(JsonResponse::HTTP_NOT_MODIFIED, $response->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::__construct
     */
    public function testGetSerialNumbersThrowsWhenEventWasNotHandledCorrectly(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DeviceRequestUpdatedPassesEvent::class))
            ->will($this->returnCallback(function(DeviceRequestUpdatedPassesEvent $event) {
                $event->notAuthorized();

                return $event;
            }));

        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('DeviceRequestUpdatedPassesEvent was not handled correctly. Unexpected status was set.');

        $controller = new DeviceController($eventDispatcher);
        $controller->getSerialNumbers($deviceLibraryIdentifier, $passTypeIdentifier);
    }
}