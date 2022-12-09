<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\Device;

use DateTimeImmutable;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\AuthenticationToken;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}", methods={"GET"})
 */
class SerialNumbersController
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

        throw new LogicException('DeviceRequestUpdatedPassesEvent was not handled correctly. Unexpected status was set.');
    }
}
