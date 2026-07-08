Apple Passbook Bundle
===============
This package provides Symfony integration for the [LauLamanApps Apple Passbook Package][LauLamanAppsApplePassbookPackage].

[![GithubCi](https://github.com/LauLamanApps/apple-passbook-bundle/workflows/CI/badge.svg)](https://github.com/LauLamanApps/apple-passbook-bundle/actions?query=workflow%3ACI)
[![Latest Stable Version](https://poser.pugx.org/laulamanapps/apple-passbook-bundle/v/stable)](https://packagist.org/packages/laulamanapps/apple-passbook-bundle)
[![License](https://poser.pugx.org/laulamanapps/apple-passbook-bundle/license)](https://packagist.org/packages/laulamanapps/apple-passbook-bundle)

Requirements
---
- PHP 8.1+
- Symfony 6.4, 7.x, or 8.x

Installation
---
```bash
composer require laulamanapps/apple-passbook-bundle
```

Run Tests
---
```bash
make tests-unit
make tests-functional
```

Get Certificate
---

Head over to the [Apple Developer Portal][AppleDeveloperPortal] to get yourself a certificate to sign your passbooks with.

Export the certificate and key to a `.p12` file using **Keychain Access**.

Configure Bundle
---
```yaml
# config/packages/laulamanapps_apple_passbook.yaml

laulamanapps_apple_passbook:
    certificate: '%env(APPLE_PASSBOOK_CERTIFICATE)%'
    password: '%env(APPLE_PASSBOOK_CERTIFICATE_PASSWORD)%'
    team_identifier: '%env(APPLE_PASSBOOK_TEAM_IDENTIFIER)%'
    pass_type_identifier: '%env(APPLE_PASSBOOK_PASS_TYPE_IDENTIFIER)%'
    environment: 'production' # or 'sandbox'
```

Add the ENV variables to the `.env` file:
```dotenv
###> laulamanapps/apple-passbook-bundle ###
APPLE_PASSBOOK_CERTIFICATE=config/certificates/pass.p12
APPLE_PASSBOOK_CERTIFICATE_PASSWORD=password
APPLE_PASSBOOK_PASS_TYPE_IDENTIFIER=pass.com.your.pass.identifier
APPLE_PASSBOOK_TEAM_IDENTIFIER=YOUR_TEAM_ID
APPLE_PASSBOOK_WEB_SERVICE_URL='https://example.com/'
###< laulamanapps/apple-passbook-bundle ###
```

> **Security note:** always reference the certificate password through `%env(...)%` as shown above.
> A literal password in the bundle configuration would be written into Symfony's compiled
> container in `var/cache`.

### Configuration reference

| Key                   | Required | Default        | Description                                     |
|-----------------------|----------|----------------|-------------------------------------------------|
| `certificate`         | yes      | —              | Path to the `.p12` or `.pem` certificate file   |
| `password`            | no       | `null`         | Certificate password (required for `.p12` files) |
| `team_identifier`     | no       | `null`         | Apple Team Identifier                           |
| `pass_type_identifier`| no       | `null`         | Pass Type Identifier                            |
| `environment`         | no       | `'production'` | APNs environment: `'production'` or `'sandbox'` |

Create & Compile Passbook
---
```php
namespace App\Controller;

use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbook\GenericPassbook;
use LauLamanApps\ApplePassbook\MetaData\Barcode;
use LauLamanApps\ApplePassbook\Style\BarcodeFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PassbookController
{
    public function __construct(
        private readonly Compiler $passbookCompiler,
    ) {
    }

    #[Route('/download/passbook/', name: 'download_passbook')]
    public function download(): Response
    {
        $passbook = new GenericPassbook('8j23fm3');
        $passbook->setTeamIdentifier('<TeamId>');
        $passbook->setPassTypeIdentifier('<PassTypeId>');
        $passbook->setOrganizationName('Toy Town');
        $passbook->setDescription('Toy Town Membership');

        $barcode = new Barcode();
        $barcode->setFormat(BarcodeFormat::Pdf417);
        $barcode->setMessage('123456789');
        $passbook->setBarcode($barcode);

        $data = $this->passbookCompiler->compile($passbook);

        $response = new Response($data);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->headers->set('Content-Type', 'application/vnd.apple.pkpass');
        $response->headers->set('Content-Disposition', 'filename="passbook.pkpass"');

        return $response;
    }
}
```

Push Notifications
---
The bundle registers the `Notifier` service from the core library for sending push notifications to devices when a pass is updated:

```php
use LauLamanApps\ApplePassbook\Build\Notifier;

final class PassUpdateService
{
    public function __construct(
        private readonly Notifier $notifier,
    ) {
    }

    public function notifyDevice(string $pushToken): void
    {
        $this->notifier->notify($pushToken);
    }
}
```

The notifier uses Apple's HTTP/2 APNs API. It uses the same certificate configured for the bundle. Set `environment: 'sandbox'` in the bundle configuration when testing against the APNs sandbox.

Built-in Web Service Controllers
---
This package comes with built-in controllers for all Apple PassKit [Web Service][AppleWebService] endpoints.
It uses Symfony's `EventDispatcher` to delegate request handling to your application.

### Enable routes

Add the following to your route configuration:
```php
// config/routes/apple_passbook.php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('@ApplePassbookBundle/Resources/config/routes.php');
};
```

Or in YAML:
```yaml
# config/routes/apple_passbook.yaml
apple_passbook:
    resource: '@ApplePassbookBundle/Resources/config/routes.php'
```

### Events

The controllers dispatch events that you handle by implementing listeners or subscribers. Each event starts with `Status::Unhandled` — your listener must set the appropriate status.

| Event | Dispatched when |
|---|---|
| `DeviceRegisteredEvent` | A device registers for pass updates |
| `DeviceUnregisteredEvent` | A device unregisters from pass updates |
| `DeviceRequestUpdatedPassesEvent` | A device requests a list of updated pass serial numbers |
| `RetrieveUpdatedPassbookEvent` | A device requests an updated pass |

### Logging endpoint

Apple devices `POST` diagnostic messages to the `/v1/log` endpoint. The bundle's `LogController` handles these automatically and writes them to the PSR `LoggerInterface` (Symfony's `logger` service) at `info` level — no event or listener is required.

### Example subscriber

```php
namespace App\EventSubscriber;

use DateTimeImmutable;
use LauLamanApps\ApplePassbook\GenericPassbook;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRegisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceRequestUpdatedPassesEvent;
use LauLamanApps\ApplePassbookBundle\Event\DeviceUnregisteredEvent;
use LauLamanApps\ApplePassbookBundle\Event\RetrieveUpdatedPassbookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ApplePassbookSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DeviceRegisteredEvent::class => 'onDeviceRegistered',
            DeviceRequestUpdatedPassesEvent::class => 'onDeviceRequestUpdatedPasses',
            RetrieveUpdatedPassbookEvent::class => 'onRetrieveUpdatedPassbook',
            DeviceUnregisteredEvent::class => 'onDeviceUnregistered',
        ];
    }

    public function onDeviceRegistered(DeviceRegisteredEvent $event): void
    {
        $passbook = $this->passbookRepository->getBySerial($event->getSerialNumber());

        // isAuthenticatedBy() uses hash_equals() internally: a timing-safe comparison.
        // Never compare authentication tokens with === or !==.
        if (!$event->isAuthenticatedBy($passbook->getAuthToken())) {
            $event->notAuthorized();

            return;
        }

        // Save device registration to database

        $event->deviceRegistered();
    }

    public function onDeviceRequestUpdatedPasses(DeviceRequestUpdatedPassesEvent $event): void
    {
        $passbooks = $this->passbookRepository->getSerialsSince(
            $event->getPassTypeIdentifier(),
            $event->getDeviceLibraryIdentifier(),
            $event->getPassesUpdatedSince(),
        );

        if ($passbooks) {
            $serials = [];
            foreach ($passbooks as $passbook) {
                $serials[] = $passbook->getSerialNumber();
            }

            $event->setSerialNumbers($serials, new DateTimeImmutable());

            return;
        }

        $event->notFound();
    }

    public function onRetrieveUpdatedPassbook(RetrieveUpdatedPassbookEvent $event): void
    {
        $entity = $this->passbookRepository->findBySerial($event->getSerialNumber());

        if ($entity === null) {
            $event->notFound();

            return;
        }

        if (!$event->isAuthenticatedBy($entity->getAuthToken())) {
            $event->notAuthorized();

            return;
        }

        if ($event->getUpdatedSince() && $entity->getUpdatedAt() < $event->getUpdatedSince()) {
            $event->notModified();

            return;
        }

        $passbook = new GenericPassbook($event->getSerialNumber());
        // Build the passbook...

        $event->setPassbook($passbook, $entity->getUpdatedAt());
    }

    public function onDeviceUnregistered(DeviceUnregisteredEvent $event): void
    {
        $passbook = $this->passbookRepository->getBySerial($event->getSerialNumber());

        if (!$event->isAuthenticatedBy($passbook->getAuthToken())) {
            $event->notAuthorized();

            return;
        }

        // Remove device registration from database

        $event->deviceUnregistered();
    }
}
```

Upgrading
---

See [UPGRADE.md](UPGRADE.md) for the migration guide from 1.x to 2.0 (PHP 8.1+, Symfony 6.4+, native `Status` enum, moved `Notifier`, PHP-based route/service config) and [CHANGELOG.md](CHANGELOG.md) for the full list of changes.

Credits
---

This package has been developed by [LauLaman][LauLaman].

[LauLamanAppsApplePassbookPackage]: https://github.com/LauLamanApps/apple-passbook
[AppleDeveloperPortal]: https://developer.apple.com/account/resources/certificates/list
[AppleWebService]: https://developer.apple.com/documentation/walletpasses/adding-a-web-service-to-update-passes
[LauLaman]: https://github.com/LauLaman
