<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Unit\Event;

use LauLamanApps\ApplePassbookBundle\Event\AbstractEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent
 */
class AbstractEventTest extends TestCase
{
    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::__construct
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::getStatus
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::notAuthorized
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::notFound
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::notModified
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::alreadyRegistered
     * @covers \LauLamanApps\ApplePassbookBundle\Event\AbstractEvent::successful
     */
    public function testConstructor(): void
    {
        $event = new class extends AbstractEvent {
            public function callSuccessful(): void
            {
                $this->successful();
            }
        };

        $this->assertEquals(Status::unhandled(), $event->getStatus());

        $event->notAuthorized();
        $this->assertEquals(Status::notAuthorized(), $event->getStatus());

        $event->notFound();
        $this->assertEquals(Status::notFound(), $event->getStatus());

        $event->notModified();
        $this->assertEquals(Status::notModified(), $event->getStatus());

        $event->alreadyRegistered();
        $this->assertEquals(Status::alreadyRegistered(), $event->getStatus());

        $event->callSuccessful();
        $this->assertEquals(Status::successful(), $event->getStatus());
    }
}