<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\TestCase;

class DeviceRequestUpdatedPassesEventTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::__construct
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::getDeviceLibraryIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::getPassTypeIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::getPassesUpdatedSince
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::getLastUpdated
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::setSerialNumbers
     * @covers \LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent::getSerialNumbers

     */
    public function testConstructor(): void
    {
        $deviceLibraryIdentifier = '<deviceLibraryIdentifier>';
        $passTypeIdentifier = '<passTypeIdentifier>';
        $lastUpdatedAt = new DateTimeImmutable();

        $event = new DeviceRequestUpdatedPassesEvent($deviceLibraryIdentifier, $passTypeIdentifier);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertEquals(Status::unhandled(), $event->getStatus());
        $this->assertSame($deviceLibraryIdentifier, $event->getDeviceLibraryIdentifier());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertNull($event->getPassesUpdatedSince());
        $this->assertNull($event->getLastUpdated());

        $event->setSerialNumbers(['132', '456'], $lastUpdatedAt);
        $this->assertEquals(['132', '456'], $event->getSerialNumbers());
        $this->assertEquals($lastUpdatedAt, $event->getLastUpdated());
        $this->assertEquals(Status::successful(), $event->getStatus());
    }
}