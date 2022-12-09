<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\Device;

use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\AuthenticationToken;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}/{serialNumber}/", methods={"POST"})
 */
class RegisterController
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
        $event = new DeviceRegisteredEvent(
            $deviceLibraryIdentifier,
            $passTypeIdentifier,
            $serialNumber,
            $this->getAuthenticationToken($request),
            json_decode($request->getContent())->pushToken
        );

        /** @var Status $status */
        $status = $this->eventDispatcher->dispatch($event)->getStatus();

        if ($status->isUnhandled()) {
            throw new LogicException('DeviceRegisteredEvent was not handled. Please implement a listener for this event.');
        }

        if ($status->isNotAuthorized()) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        if ($status->isAlreadyRegistered()) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        if ($status->isSuccessful()) {
            return new JsonResponse([], Response::HTTP_CREATED);
        }

        throw new LogicException('DeviceRegisteredEvent was not handled correctly. Unexpected status was set.');
    }
}
