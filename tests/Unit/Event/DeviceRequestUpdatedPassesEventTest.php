<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceRequestUpdatedPassesEvent::class)]
class DeviceRequestUpdatedPassesEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $lastUpdatedAt = new DateTimeImmutable();

        $event = new DeviceRequestUpdatedPassesEvent($deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertSame(Status::Unhandled, $event->getStatus());
        $this->assertSame($deviceLibraryIdentifier, $event->getDeviceLibraryIdentifier());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertNull($event->getPassesUpdatedSince());
        $this->assertNull($event->getLastUpdated());

        $event->setSerialNumbers(['132', '456'], $lastUpdatedAt);
        $this->assertEquals(['132', '456'], $event->getSerialNumbers());
        $this->assertEquals($lastUpdatedAt, $event->getLastUpdated());
        $this->assertSame(Status::Successful, $event->getStatus());
    }
}
