<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    private Status $status;

    public function __construct()
    {
        $this->status = Status::Unhandled;
    }

    final public function notAuthorized(): void
    {
        $this->status = Status::NotAuthorized;
    }

    final public function notFound(): void
    {
        $this->status = Status::NotFound;
    }

    final public function notModified(): void
    {
        $this->status = Status::NotModified;
    }

    final public function alreadyRegistered(): void
    {
        $this->status = Status::AlreadyRegistered;
    }

    final protected function successful(): void
    {
        $this->status = Status::Successful;
    }

    final public function getStatus(): Status
    {
        return $this->status;
    }
}
