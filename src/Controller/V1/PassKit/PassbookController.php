<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use LauLamanApps\ApplePassbookBundle\Event\Status;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PassbookController
{
    use AuthenticationToken;

    public function __construct(
        private readonly Compiler $compiler,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getUpdatedPassbook(Request $request, string $passTypeIdentifier, string $serialNumber): Response
    {
        $event = new RetrieveUpdatedPassbookEvent(
            $passTypeIdentifier,
            $serialNumber,
            $this->getAuthenticationToken($request),
            $this->parseIfModifiedSince($request),
        );
        $this->eventDispatcher->dispatch($event);

        if ($event->getStatus() === Status::Unhandled) {
            throw new LogicException('RetrieveUpdatedPassbookEvent was not handled. Please implement a listener for this event.');
        }

        if ($event->getStatus() === Status::NotAuthorized) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        if ($event->getStatus() === Status::NotFound) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        if ($event->getStatus() === Status::NotModified) {
            return new JsonResponse([], Response::HTTP_NOT_MODIFIED);
        }

        if ($event->getStatus() === Status::Successful) {
            $data = $this->compiler->compile($event->getPassbook());
            $lastModified = $event->getLastModified();
            $lastModified = $lastModified->setTimezone(new \DateTimeZone('GMT'));

            $response = new Response($data);
            $response->headers->set('Content-Description', 'File Transfer');
            $response->headers->set('Content-Type', 'application/vnd.apple.pkpass');
            $response->headers->set('Content-Disposition', 'filename="pass.pkpass"');
            $response->headers->set('Last-Modified', $lastModified->format('D, d M Y H:i:s \G\M\T'));

            return $response;
        }

        throw new LogicException('RetrieveUpdatedPassbookEvent was not handled correctly. Unexpected status was set.');
    }

    private function parseIfModifiedSince(Request $request): ?\DateTimeImmutable
    {
        $ifModifiedSince = $request->headers->get('If-Modified-Since');

        if ($ifModifiedSince === null || $ifModifiedSince === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($ifModifiedSince);
        } catch (\Exception) {
            return null;
        }
    }
}
