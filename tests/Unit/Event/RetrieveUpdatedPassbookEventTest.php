<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\Passbook;
use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\TestCase;

class RetrieveUpdatedPassbookEventTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::__construct
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::getPassTypeIdentifier
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::getSerialNumber
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::getAuthenticationToken
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::getUpdatedSince
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::setPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::getPassbook
     * @covers \LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent::getLastModified
     */
    public function testConstructor(): void
    {
        $passTypeIdentifier = '<passTypeIdentifier>';
        $serialNumber = '<serialNumber>';
        $authenticationToken = '<authenticationToken>';
        $updatedSince = new DateTimeImmutable();

        $passbook = $this->createMock(Passbook::class);
        $lastModified = new DateTimeImmutable();

        $event = new RetrieveUpdatedPassbookEvent($passTypeIdentifier, $serialNumber, $authenticationToken, $updatedSince);

        $this->assertInstanceOf(AbstractEvent::class, $event);
        $this->assertEquals(Status::unhandled(), $event->getStatus());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertSame($serialNumber, $event->getSerialNumber());
        $this->assertSame($authenticationToken, $event->getAuthenticationToken());
        $this->assertSame($updatedSince, $event->getUpdatedSince());

        $event->setPassbook($passbook, $lastModified);
        $this->assertEquals(Status::successful(), $event->getStatus());
        $this->assertEquals($passbook, $event->getPassbook());
        $this->assertEquals($lastModified, $event->getLastModified());
    }
}