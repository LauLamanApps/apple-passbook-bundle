<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    /**
     * @var Status
     */
    private $status;

    public function __construct()
    {
        $this->status = Status::unhandled();
    }

    final public function notAuthorized(): void
    {
        $this->status = Status::notAuthorized();
    }

    final public function notFound(): void
    {
        $this->status = Status::notFound();
    }

    final public function notModified(): void
    {
        $this->status = Status::notModified();
    }

    final public function alreadyRegistered(): void
    {
        $this->status = Status::alreadyRegistered();
    }

    final protected function successful(): void
    {
        $this->status = Status::successful();
    }

    final public function getStatus(): Status
    {
        return $this->status;
    }
}
