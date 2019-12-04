<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Event;


final class DeviceUnregisteredEvent extends AbstractEvent
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

    public function __construct(
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier,
        string $serialNumber,
        string $authenticationToken
    ) {
        $this->deviceLibraryIdentifier = $deviceLibraryIdentifier;
        $this->passTypeIdentifier = $passTypeIdentifier;
        $this->serialNumber = $serialNumber;
        $this->authenticationToken = $authenticationToken;

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

    public function deviceUnregistered(): void
    {
        $this->successful();
    }
}
