<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/v1/passes/{passTypeIdentifier}/{serialNumber}")
 */
class PassbookController extends AbstractController
{
    use AuthenticationToken;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(Compiler $compiler, EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->compiler = $compiler;
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function getUpdatedPassbook(Request $request, string $passTypeIdentifier, string $serialNumber): Response
    {
        $event = new RetrieveUpdatedPassbookEvent(
            $passTypeIdentifier,
            $serialNumber,
            $this->getAuthenticationToken($request)
        );
        $this->eventDispatcher->dispatch($event);

        if ($event->getStatus()->isUnhandled()) {
            throw new LogicException('RetrieveUpdatedPassbookEvent was not handled. Please implement a listener for this event.');
        }

        if ($event->getStatus()->isNotAuthorized()) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        if ($event->getStatus()->isNotModified()) {
            return new JsonResponse([], Response::HTTP_NOT_MODIFIED);
        }

        if ($event->getStatus()->isSuccessful()) {
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
}


