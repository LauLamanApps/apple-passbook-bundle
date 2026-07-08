<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceUnregisteredEvent::class)]
class DeviceUnregisteredEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';

        $event = new DeviceUnregisteredEvent($deviceLibraryIdentifier, $passTypeIdentifier, $serialNumber, $authenticationToken);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertSame(Status::Unhandled, $event->getStatus());
        $this->assertSame($deviceLibraryIdentifier, $event->getDeviceLibraryIdentifier());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertSame($serialNumber, $event->getSerialNumber());
        $this->assertSame($authenticationToken, $event->getAuthenticationToken());

        $event->deviceUnregistered();
        $this->assertSame(Status::Successful, $event->getStatus());
    }
}
