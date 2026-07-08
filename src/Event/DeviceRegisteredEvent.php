<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

final class DeviceRegisteredEvent extends AbstractEvent
{
    public function __construct(
        private readonly string $deviceLibraryIdentifier,
        private readonly string $passTypeIdentifier,
        private readonly string $serialNumber,
        private readonly string $authenticationToken,
        private readonly string $pushToken,
    ) {
        parent::__construct();
    }

    public function getDeviceLibraryIdentifier(): string
    {
        return $this->deviceLibraryIdentifier;
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

    public function getPushToken(): string
    {
        return $this->pushToken;
    }

    public function deviceRegistered(): void
    {
        $this->successful();
    }
}
