<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceRegisteredEvent::class)]
#[CoversClass(DeviceUnregisteredEvent::class)]
#[CoversClass(RetrieveUpdatedPassbookEvent::class)]
class EventAuthenticationTest extends TestCase
{
    public function testDeviceRegisteredEventIsAuthenticatedBy(): void
    {
        $event = new DeviceRegisteredEvent('<device>', '<passType>', '<serial>', 'secret-token', '<pushToken>');

        self::assertTrue($event->isAuthenticatedBy('secret-token'));
        self::assertFalse($event->isAuthenticatedBy('wrong-token'));
        self::assertFalse($event->isAuthenticatedBy(''));
    }

    public function testDeviceUnregisteredEventIsAuthenticatedBy(): void
    {
        $event = new DeviceUnregisteredEvent('<device>', '<passType>', '<serial>', 'secret-token');

        self::assertTrue($event->isAuthenticatedBy('secret-token'));
        self::assertFalse($event->isAuthenticatedBy('wrong-token'));
        self::assertFalse($event->isAuthenticatedBy(''));
    }

    public function testRetrieveUpdatedPassbookEventIsAuthenticatedBy(): void
    {
        $event = new RetrieveUpdatedPassbookEvent('<passType>', '<serial>', 'secret-token');

        self::assertTrue($event->isAuthenticatedBy('secret-token'));
        self::assertFalse($event->isAuthenticatedBy('wrong-token'));
        self::assertFalse($event->isAuthenticatedBy(''));
    }

    public function testEmptyExpectedTokenNeverAuthenticatesEvenWhenRequestTokenIsEmpty(): void
    {
        $event = new RetrieveUpdatedPassbookEvent('<passType>', '<serial>', '');

        self::assertFalse($event->isAuthenticatedBy(''));
    }
}
