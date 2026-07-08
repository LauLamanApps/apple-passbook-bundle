<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\Passbook;

final class RetrieveUpdatedPassbookEvent extends AbstractEvent
{
    private ?Passbook $passbook = null;
    private ?DateTimeImmutable $lastModified = null;

    public function __construct(
        private readonly string $passTypeIdentifier,
        private readonly string $serialNumber,
        private readonly string $authenticationToken,
        private readonly ?DateTimeImmutable $updatedSince = null,
    ) {
        parent::__construct();
    }

    public function setPassbook(Passbook $passbook, DateTimeImmutable $lastModified): void
    {
        $this->successful();
        $this->lastModified = $lastModified;
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

    /**
     * Timing-safe comparison of the request's authentication token against the expected token.
     */
    public function isAuthenticatedBy(#[\SensitiveParameter] string $expectedToken): bool
    {
        return $expectedToken !== '' && hash_equals($expectedToken, $this->authenticationToken);
    }

    public function getUpdatedSince(): ?DateTimeImmutable
    {
        return $this->updatedSince;
    }

    public function getPassbook(): ?Passbook
    {
        return $this->passbook;
    }

    public function getLastModified(): ?DateTimeImmutable
    {
        return $this->lastModified;
    }
}
