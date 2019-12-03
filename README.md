Apple Passbook Bundle
===============
This package provides Symfony configuration for [LauLamanApps Apple Passbook Package][LauLamanAppsApplePassbookPackage]

[![GithubCi](https://github.com/LauLamanApps/apple-passbook-bundle/workflows/CI/badge.svg)](https://github.com/LauLamanApps/apple-passbook-bundle/actions?query=workflow%3ACI)
[![Build Status](https://scrutinizer-ci.com/g/LauLamanApps/apple-passbook-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/LauLamanApps/apple-passbook-bundle/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/LauLamanApps/apple-passbook-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/LauLamanApps/apple-passbook-bundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LauLamanApps/apple-passbook-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LauLamanApps/apple-passbook-bundle/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/laulamanapps/apple-passbook-bundle/v/stable)](https://packagist.org/packages/laulamanapps/apple-passbook)
[![License](https://poser.pugx.org/laulamanapps/apple-passbook-bundle/license)](https://packagist.org/packages/laulamanapps/apple-passbook)

Installation
---
With [composer](http://packagist.org), add:

```bash
$ composer require laulamanapps/apple-passbook-bundle
```

Run Tests
---
To make sure everything works you can run tests:

```bash
$ make tests-unit 
$ make tests-integration 
$ make tests-infection 
```

Get certificate
---

Head over to the [Apple Developer Portal][AppleDeveloperPortal] to get yourself a certificate to sign your passbooks with.

[Convert](docs/certificate.md) the certificate and key to a .p12 file using the **Keychain Access**

Configure Bundle
---
```yaml
#config/packages/laulamanapps_apple_passbook.yml

laulamanapps_apple_passbook:
  certificate: '%env(APPLE_PASSBOOK_CERTIFICATE)%'
  password: '%env(APPLE_PASSBOOK_CERTIFICATE_PASSWORD)%'
  team_identifier: '%env(APPLE_PASSBOOK_TEAM_IDENTIFIER)%'
  pass_type_identifier: '%env(APPLE_PASSBOOK_PASS_TYPE_IDENTIFIER)%'
```

Add the ENV variables to the `.env` file
```dotenv
##> laulamanapps/apple-passbook-bundle
APPLE_PASSBOOK_CERTIFICATE=path/to/certificate.p12
APPLE_PASSBOOK_CERTIFICATE_PASSWORD=password
APPLE_PASSBOOK_PASS_TYPE_IDENTIFIER=pass.com.your.pass.identifiers
APPLE_PASSBOOK_TEAM_IDENTIFIER=identifier
APPLE_PASSBOOK_WEB_SERVICE_URL='http://example.com/'
##< laulamanapps/apple-passbook-bundle
```

Create & Compile Passbook
---
```php
namespace App\Controller;

use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbook\GenericPassbook;
use LauLamanApps\ApplePassbook\MetaData\Barcode;
use LauLamanApps\ApplePassbook\Style\BarcodeFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class PassbookController extends AbstractController
{
    /**
     * @var Compiler
     */
    private $passbookCompiler;

    public function __construct(Compiler $passbookCompiler) {
        $this->passbookCompiler = $passbookCompiler;
    }

    /**
     * @Route("/download/passbook/", name="download_passbook")
     */
    public function download(): Response
    {
        $passbook = new GenericPassbook('8j23fm3');
        $passbook->setTeamIdentifier('<TeamId>');
        $passbook->setPassTypeIdentifier('<PassTypeId>');
        $passbook->setOrganizationName('Toy Town');
        $passbook->setDescription('Toy Town Membership');
        
        $barcode = new Barcode();
        $barcode->setFormat(BarcodeFormat::pdf417());
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

Configure Build in Webservices
---
This package comes with build in controllers for all Apple passbooks webservice URLs.
It is using Symfonys build in `EventDispatcher`. 

Enable this by adding the following configuration to the `config/routes/routes.yaml`  
```yaml
passbook_routes:
    resource: '@ApplePassbookBundle/Controller/'
    type:     annotation
```
The controllers will dispatch the following events, the following information is available:
```php
/* Available on All events */
$event->getPassTypeIdentifier();
$event->getStatus();

/* Available on DeviceRegisteredEvent */
$event->getAuthenticationToken();
$event->getDeviceLibraryIdentifier();
$event->getSerialNumber();

/* Available on DeviceRequestUpdatedPassesEvent */
$event->getAuthenticationToken();
$event->getDeviceLibraryIdentifier();
$event->getPassesUpdatedSince();

/* Available on DeviceUnregisteredEvent */
$event->getAuthenticationToken();
$event->getDeviceLibraryIdentifier();
$event->getSerialNumber();

/* Available on RetrieveUpdatedPassbookEvent */
$event->getAuthenticationToken();
$event->getSerialNumber();
$event->getPassTypeIdentifier();
$event->getUpdatedSince();
```

Now Subscribe to the events:

The idea here is that you handle the event and mark the events as handled by calling setters on the event itself.
The event by default has the status `Status::unhandled()`

```php
namespace App\Integration\Symfony\EventSubscriber;

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

        if ($event->getAuthenticationToken() <> $passbook->getAuthToken()) {
            $event->notAuthorized();

            return;
        }

        /**
         * Save in Database 
         */

        $event->deviceRegistered();
    }

    public function onDeviceRequestUpdatedPasses(DeviceRequestUpdatedPassesEvent $event): void
    {
        $passbooks = $this->passbookRepository->getSerialsSince(
            $event->getPassTypeIdentifier(),
            $event->getDeviceLibraryIdentifier(),
            $event->getPassesUpdatedSince()
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
        try {
            $entity = $this->passbookRepository->getBySerial($event->getSerialNumber());
            if ($entity->getAuthToken() !== $event->getAuthenticationToken()) {
                $event->notAuthorized();

                return;
            }

            if ($event->getUpdatedSince() && $entity->getUpdatedAt() < $event->getUpdatedSince()) {
                $event->notModified();

                return;
            }

            $passbook = new GenericPassbook($event->getSerialNumber());
            /* Generate Passbook */

            $event->setPassbook($passbook);
        } catch (NoResultException $e) {
            $event->notFound();
        }
    }

    public function onDeviceUnregistered(DeviceUnregisteredEvent $event): void
    {
        $passbook = $this->passbookRepository->getBySerial($event->getSerialNumber());

        if ($event->getAuthenticationToken() <> $passbook->getAuthToken()) {
            $event->notAuthorized();

            return;
        }

        /**
         * Remove from Database 
         */

        $event->deviceUnregistered();
    }
}

```

Credits
---

This package has been developed by [LauLaman][LauLaman].

[LauLamanAppsApplePassbookPackage]: https://github.com/LauLamanApps/apple-passbook
[AppleDeveloperPortal]: https://developer.apple.com/account/resources/certificates/list
[LauLaman]: https://github.com/LauLaman