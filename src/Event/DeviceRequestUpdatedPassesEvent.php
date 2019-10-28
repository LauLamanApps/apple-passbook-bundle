<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use DateTimeImmutable;

final class DeviceRequestUpdatedPassesEvent extends AbstractEvent
{
    /**
     * @var string
     */
    private $deviceLibraryIdentifier;

    /**
     * @var string
     */
    private $passTypeIdentifier;

    /**
     * @var DateTimeImmutable|null
     */
    private $passesUpdatedSince;

    /**
     * @var DateTimeImmutable|null
     */
    private $lastUpdated;

    /**
     * @var string[]
     */
    private $serialNumbers;

    public function __construct(string $deviceLibraryIdentifier, string $passTypeIdentifier)
    {
        $this->deviceLibraryIdentifier = $deviceLibraryIdentifier;
        $this->passTypeIdentifier = $passTypeIdentifier;
        parent::__construct();
    }

    public function setSerialNumbers(array $serialNumbers, DateTimeImmutable $lastUpdated): void
    {
        $this->successful();
        $this->serialNumbers = $serialNumbers;
        $this->lastUpdated = $lastUpdated;
    }

    public function getDeviceLibraryIdentifier(): string
    {
        return $this->deviceLibraryIdentifier;
    }

    public function getPassTypeIdentifier(): string
    {
        return $this->passTypeIdentifier;
    }

    public function getPassesUpdatedSince(): ?DateTimeImmutable
    {
        return $this->passesUpdatedSince;
    }

    public function getLastUpdated(): ?DateTimeImmutable
    {
        return $this->lastUpdated;
    }

    public function getSerialNumbers(): array
    {
        return $this->serialNumbers;
    }
}
