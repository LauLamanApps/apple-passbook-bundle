<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent
 */
class DeviceUnregisteredEventTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent::__construct
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent::getDeviceLibraryIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent::getPassTypeIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent::getSerialNumber
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent::getAuthenticationToken
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent::deviceUnregistered
     */
    public function testConstructor(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $event = new DeviceUnregisteredEvent($deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber, $authenticationToken);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertEquals(Status::unhandled(), $event->getStatus());
        $this->assertSame($deviceLibraryIdentifier, $event->getDeviceLibraryIdentifier());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertSame($serialNumber, $event->getSerialNumber());
        $this->assertSame($authenticationToken, $event->getAuthenticationToken());

        $event->deviceUnregistered();
        $this->assertEquals(Status::successful(), $event->getStatus());
    }
}