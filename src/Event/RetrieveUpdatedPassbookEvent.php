<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\Passbook;

final class RetrieveUpdatedPassbookEvent extends AbstractEvent
{
    /**
     * @var string
     */
    private $passTypeIdentifier;

    /**
     * @var string
     */
    private $serialNumber;

    /**
     * @var string
     */
    private $authenticationToken;

    /**
     * @var DateTimeImmutable|null
     */
    private $updatedSince;

    /**
     * @var Passbook
     */
    private $passbook;

    public function __construct(string $passTypeIdentifier, string $serialNumber, string $authenticationToken, ?DateTimeImmutable $updatedSince = null)
    {
        parent::__construct();
        $this->passTypeIdentifier = $passTypeIdentifier;
        $this->serialNumber = $serialNumber;
        $this->authenticationToken = $authenticationToken;
        $this->updatedSince = $updatedSince;
    }

    public function setPassbook(Passbook $passbook): void
    {
        $this->successful();
        $this->passbook = $passbook;
    }

    public function getPassTypeIdentifier(): string
    {
        return $this->passTypeIdentifier;
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    public function getAuthenticationToken(): string
    {
        return $this->authenticationToken;
    }

    public function getUpdatedSince(): ?DateTimeImmutable
    {
        return $this->updatedSince;
    }

    public function getPassbook(): Passbook
    {
        return $this->passbook;
    }
}
