<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceRegisteredEvent::class)]
class DeviceRegisteredEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $pushToken = '<authenticationToken>';

        $event = new DeviceRegisteredEvent($deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber, $authenticationToken, $pushToken);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertSame(Status::Unhandled, $event->getStatus());
        $this->assertSame($deviceLibraryIdentifier, $event->getDeviceLibraryIdentifier());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertSame($serialNumber, $event->getSerialNumber());
        $this->assertSame($authenticationToken, $event->getAuthenticationToken());
        $this->assertSame($pushToken, $event->getPushToken());

        $event->deviceRegistered();
        $this->assertSame(Status::Successful, $event->getStatus());
    }
}
