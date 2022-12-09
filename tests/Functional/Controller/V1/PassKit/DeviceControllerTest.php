<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Functional\Controller\V1\PassKit;

use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Tests\Functional\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController
 */
class DeviceControllerTest extends TestCase
{
    /**
     * @var TestKernel
     */
    private $kernel;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function setUp(): void
    {
        $this->kernel = new TestKernel();
        $this->kernel->boot();
        $this->client = new KernelBrowser($this->kernel);
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::unregister
     * @dataProvider unAllowedMethodsForDeviceEndPoint
     */
    public function testDeviceEndpointCalledWithWrongMethodReturns405($method): void
    {
        $uri = '/v1/devices/<deviceLibraryIdentifier>/registrations/<passTypeIdentifier>/<serialNumber>';

        $this->client->request($method, $uri);

        $this->assertSame(405, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::register
     */
    public function testRegisterDispatchesEvent(): void
    {
        $uri = '/v1/devices/<deviceLibraryIdentifier>/registrations/<passTypeIdentifier>/<serialNumber>';

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(DeviceRegisteredEvent::class, function (DeviceRegisteredEvent $event) {
            $event->deviceRegistered();
        });

        $this->client->request(Request::METHOD_POST, $uri,[],[],[], json_encode(['pushToken' => '<pushToken>']));

        $this->assertSame(201, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::unregister
     */
    public function testUnRegisterDispatchesEvent(): void
    {
        $uri = '/v1/devices/<deviceLibraryIdentifier>/registrations/<passTypeIdentifier>/<serialNumber>';

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(DeviceUnregisteredEvent::class, function (DeviceUnregisteredEvent $event) {
            $event->deviceUnregistered();
        });

        $this->client->request(Request::METHOD_DELETE, $uri);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     * @dataProvider unAllowedMethodsForDevicesEndPoint
     */
    public function testDevicesEndpointCalledWithWrongMethodReturns405($method): void
    {
        $uri = '/v1/devices/<deviceLibraryIdentifier>/registrations/<passTypeIdentifier>';

        $this->client->request($method, $uri);

        $this->assertSame(405, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @covers \LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController::getSerialNumbers
     */
    public function testGetSerialNumbersDispatchesEvent(): void
    {
        $uri = '/v1/devices/<deviceLibraryIdentifier>/registrations/<passTypeIdentifier>';

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(DeviceRequestUpdatedPassesEvent::class, function (DeviceRequestUpdatedPassesEvent $event) {
            $event->setSerialNumbers([123], new \DateTimeImmutable());
        });

        $this->client->request(Request::METHOD_GET, $uri);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function unAllowedMethodsForDeviceEndPoint(): array
    {
        return [
            'HEAD' => [Request::METHOD_HEAD],
            'GET' => [Request::METHOD_GET],
            'PUT' => [Request::METHOD_PUT],
            'PATCH' => [Request::METHOD_PATCH],
            'PURGE' => [Request::METHOD_PURGE],
            'OPTIONS' => [Request::METHOD_OPTIONS],
            'TRACE' => [Request::METHOD_TRACE],
            'CONNECT' => [Request::METHOD_CONNECT],
        ];
    }

    public function unAllowedMethodsForDevicesEndPoint(): array
    {
        return [
            'POST' => [Request::METHOD_POST],
            'PUT' => [Request::METHOD_PUT],
            'PATCH' => [Request::METHOD_PATCH],
            'DELETE' => [Request::METHOD_DELETE],
            'PURGE' => [Request::METHOD_PURGE],
            'OPTIONS' => [Request::METHOD_OPTIONS],
            'TRACE' => [Request::METHOD_TRACE],
            'CONNECT' => [Request::METHOD_CONNECT],
        ];
    }
}
