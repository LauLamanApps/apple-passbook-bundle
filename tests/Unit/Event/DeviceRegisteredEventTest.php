<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\TestCase;

class DeviceRegisteredEventTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::__construct
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::getDeviceLibraryIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::getPassTypeIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::getSerialNumber
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::getAuthenticationToken
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::getPushToken
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent::deviceRegistered
     */
    public function testConstructor(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<authenticationToken>';

        $event = new DeviceRegisteredEvent($deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber, $authenticationToken, $pushToken);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertEquals(Status::unhandled(), $event->getStatus());
        $this->assertSame($deviceLibraryIdentifier, $event->getDeviceLibraryIdentifier());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertSame($serialNumber, $event->getSerialNumber());
        $this->assertSame($authenticationToken, $event->getAuthenticationToken());
        $this->assertSame($pushToken, $event->getPushToken());

        $event->deviceRegistered();
        $this->assertEquals(Status::successful(), $event->getStatus());
    }
}