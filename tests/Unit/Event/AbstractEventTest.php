<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractEvent::class)]
class AbstractEventTest extends TestCase
{
    public function testConstructor(): void
    {
        $event = new class extends AbstractEvent {
            public function callSuccessful(): void
            {
                $this->successful();
            }
        };

        $this->assertSame(Status::Unhandled, $event->getStatus());

        $event->notAuthorized();
        $this->assertSame(Status::NotAuthorized, $event->getStatus());

        $event->notFound();
        $this->assertSame(Status::NotFound, $event->getStatus());

        $event->notModified();
        $this->assertSame(Status::NotModified, $event->getStatus());

        $event->alreadyRegistered();
        $this->assertSame(Status::AlreadyRegistered, $event->getStatus());

        $event->callSuccessful();
        $this->assertSame(Status::Successful, $event->getStatus());
    }
}
