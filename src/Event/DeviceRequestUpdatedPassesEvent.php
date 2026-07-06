<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use DateTimeImmutable;

final class DeviceRequestUpdatedPassesEvent extends AbstractEvent
{
    private ?DateTimeImmutable $lastUpdated = null;

    /** @var string[] */
    private array $serialNumbers = [];

    public function __construct(
        private readonly string $deviceLibraryIdentifier,
        private readonly string $passTypeIdentifier,
        private readonly ?DateTimeImmutable $passesUpdatedSince = null,
    ) {
        parent::__construct();
    }

    /**
     * @param string[] $serialNumbers
     */
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

    /**
     * @return string[]
     */
    public function getSerialNumbers(): array
    {
        return $this->serialNumbers;
    }
}
