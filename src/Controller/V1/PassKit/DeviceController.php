<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}")
 */
class DeviceController extends AbstractController
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

    /**
     * @Route("/{serialNumber}", methods={"POST"})
     */
    public function registerDevice(
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

        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{serialNumber}", methods={"DELETE"})
     */
    public function unregister(
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

        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function getSerialNumbers(
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier
    ): JsonResponse {
        $event = new DeviceRequestUpdatedPassesEvent($deviceLibraryIdentifier, $passTypeIdentifier);
        $this->eventDispatcher->dispatch($event);

        if ($event->getStatus()->isUnhandled()) {
            throw new LogicException('DeviceRequestUpdatedPassesEvent was not handled. Please implement a listener for this event.');
        }

        if ($event->getStatus()->isNotFound()) {
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        if ($event->getStatus()->isNotModified()) {
            return new JsonResponse([], Response::HTTP_NOT_MODIFIED);
        }

        if ($event->getStatus()->isSuccessful()) {
            return new JsonResponse([
                'lastUpdated' => $event->getLastUpdated()->format(DateTimeImmutable::ATOM),
                'serialNumbers' => $event->getSerialNumbers()
            ]);
        }

        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }
}
