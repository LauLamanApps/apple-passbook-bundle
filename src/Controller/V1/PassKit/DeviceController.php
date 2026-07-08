<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController
{
    use AuthenticationToken;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function register(
        Request $request,
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier,
        string $serialNumber,
    ): JsonResponse {
        $content = json_decode($request->getContent(), true);
        $pushToken = is_array($content) ? ($content['pushToken'] ?? null) : null;

        if (!is_string($pushToken) || $pushToken === '') {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $event = new DeviceRegisteredEvent(
            $deviceLibraryIdentifier,
            $passTypeIdentifier,
            $serialNumber,
            $this->getAuthenticationToken($request),
            $pushToken,
        );

        $status = $this->eventDispatcher->dispatch($event)->getStatus();

        if ($status === Status::Unhandled) {
            throw new LogicException('DeviceRegisteredEvent was not handled. Please implement a listener for this event.');
        }

        if ($status === Status::NotAuthorized) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        if ($status === Status::AlreadyRegistered) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        if ($status === Status::Successful) {
            return new JsonResponse([], Response::HTTP_CREATED);
        }

        throw new LogicException('DeviceRegisteredEvent was not handled correctly. Unexpected status was set.');
    }

    public function unregister(
        Request $request,
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier,
        string $serialNumber,
    ): JsonResponse {
        $event = new DeviceUnregisteredEvent(
            $deviceLibraryIdentifier,
            $passTypeIdentifier,
            $serialNumber,
            $this->getAuthenticationToken($request),
        );

        $status = $this->eventDispatcher->dispatch($event)->getStatus();

        if ($status === Status::Unhandled) {
            throw new LogicException('DeviceUnregisteredEvent was not handled. Please implement a listener for this event.');
        }

        if ($status === Status::NotAuthorized) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        if ($status === Status::Successful) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        throw new LogicException('DeviceUnregisteredEvent was not handled correctly. Unexpected status was set.');
    }

    public function getSerialNumbers(
        Request $request,
        string $deviceLibraryIdentifier,
        string $passTypeIdentifier,
    ): JsonResponse {
        $event = new DeviceRequestUpdatedPassesEvent(
            $deviceLibraryIdentifier,
            $passTypeIdentifier,
            $this->parsePassesUpdatedSince($request),
        );
        $this->eventDispatcher->dispatch($event);

        if ($event->getStatus() === Status::Unhandled) {
            throw new LogicException('DeviceRequestUpdatedPassesEvent was not handled. Please implement a listener for this event.');
        }

        if ($event->getStatus() === Status::NotFound) {
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        if ($event->getStatus() === Status::NotModified) {
            return new JsonResponse([], Response::HTTP_NOT_MODIFIED);
        }

        if ($event->getStatus() === Status::Successful) {
            return new JsonResponse([
                'lastUpdated' => $event->getLastUpdated()->format(DateTimeImmutable::ATOM),
                'serialNumbers' => $event->getSerialNumbers(),
            ]);
        }

        throw new LogicException('DeviceRequestUpdatedPassesEvent was not handled correctly. Unexpected status was set.');
    }

    private function parsePassesUpdatedSince(Request $request): ?DateTimeImmutable
    {
        $passesUpdatedSince = $request->query->get('passesUpdatedSince');

        if (!is_string($passesUpdatedSince) || $passesUpdatedSince === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($passesUpdatedSince);
        } catch (\Exception) {
            return null;
        }
    }
}
