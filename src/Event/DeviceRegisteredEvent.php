<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;

final class DeviceRegisteredEvent extends AbstractEvent
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
     * @var string
     */
    private $serialNumber;

    /**
     * @var string
     */
    private $authenticationToken;

    /**
     * @var string
     */
    private $pushToken;

    public function __construct(
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier,
        string $serialNumber,
        string $authenticationToken,
        string $pushToken
    ) {
        parent::__construct();
        $this->deviceLibraryIdentifier = $deviceLibraryIdentifier;
        $this->passTypeIdentifier = $passTypeIdentifier;
        $this->serialNumber = $serialNumber;
        $this->authenticationToken = $authenticationToken;
        $this->pushToken = $pushToken;
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

    public function getPushToken(): string
    {
        return $this->pushToken;
    }

    public function deviceRegistered(): void
    {
        $this->successful();
    }
}
