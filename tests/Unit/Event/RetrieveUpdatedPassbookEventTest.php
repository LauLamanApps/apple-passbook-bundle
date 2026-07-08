<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\Passbook;
use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RetrieveUpdatedPassbookEvent::class)]
class RetrieveUpdatedPassbookEventTest extends TestCase
{
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
        $this->assertSame(Status::Unhandled, $event->getStatus());
        $this->assertSame($passTypeIdentifier, $event->getPassTypeIdentifier());
        $this->assertSame($serialNumber, $event->getSerialNumber());
        $this->assertSame($authenticationToken, $event->getAuthenticationToken());
        $this->assertSame($updatedSince, $event->getUpdatedSince());

        $event->setPassbook($passbook, $lastModified);
        $this->assertSame(Status::Successful, $event->getStatus());
        $this->assertEquals($passbook, $event->getPassbook());
        $this->assertEquals($lastModified, $event->getLastModified());
    }
}
