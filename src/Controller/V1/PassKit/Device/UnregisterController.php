<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\Device;

use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\AuthenticationToken;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}/{serialNumber}", methods={"DELETE"})
 */
class UnregisterController
{
    use AuthenticationToken;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(
        Request $request,
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier,
        string $serialNumber
    ): JsonResponse {
        $event = new DeviceUnregisteredEvent(
            $deviceLibraryIdentifier,
            $passTypeIdentifier,
            $serialNumber,
            $this->getAuthenticationToken($request)
        );

        /** @var Status $status */
        $status = $this->eventDispatcher->dispatch($event)->getStatus();

        if ($status->isUnhandled()) {
            throw new LogicException('DeviceUnregisteredEvent was not handled. Please implement a listener for this event.');
        }

        if ($status->isNotAuthorized()) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        if ($status->isSuccessful()) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        throw new LogicException('DeviceUnregisteredEvent was not handled correctly. Unexpected status was set.');
    }
}
