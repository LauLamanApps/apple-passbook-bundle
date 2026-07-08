# Upgrading to 2.0

## Requirements

- **PHP 8.1 or higher** is now required (was `>=7.3`)
- **Symfony 6.4 or higher** is now required (was `^5.1`)
- **`laulamanapps/apple-passbook` 2.0** is now required (was `^1.1`)
- The `werkspot/enum` package is no longer used

## Status Enum

The `Status` class has been converted from a `werkspot/enum` `AbstractEnum` to a native PHP backed enum.

### Before (1.x)

```php
use LauLamanApps\ApplePassbookBundle\Event\Status;

// Creating
$status = Status::unhandled();

// Comparing
if ($status->isUnhandled()) { ... }
if ($status->isSuccessful()) { ... }
if ($status->isNotAuthorized()) { ... }
```

### After (2.0)

```php
use LauLamanApps\ApplePassbookBundle\Event\Status;

// The enum case
$status = Status::Unhandled;

// Comparing
if ($status === Status::Unhandled) { ... }
if ($status === Status::Successful) { ... }
if ($status === Status::NotAuthorized) { ... }
```

### Status case mapping

| 1.x method | 2.0 case |
|---|---|
| `Status::unhandled()` | `Status::Unhandled` |
| `Status::notAuthorized()` | `Status::NotAuthorized` |
| `Status::notModified()` | `Status::NotModified` |
| `Status::successful()` | `Status::Successful` |
| `Status::alreadyRegistered()` | `Status::AlreadyRegistered` |
| `Status::notFound()` | `Status::NotFound` |

### Removed methods

- `->isXxx()` methods no longer exist; use `===` comparison instead
- `->getValue()` is replaced by `->value` (native backed enum property)

## Event handling

The `AbstractEvent::getStatus()` method now returns a `Status` enum case instead of a `Status` object. If you have event listeners that check the status, update the comparisons:

```php
// Before
$status = $event->getStatus();
if ($status->isSuccessful()) { ... }

// After
$status = $event->getStatus();
if ($status === Status::Successful) { ... }
```

The setter methods on events (`notAuthorized()`, `notFound()`, `notModified()`, `alreadyRegistered()`, and the type-specific methods like `deviceRegistered()`) remain unchanged.

## Authentication token comparison

The token-carrying events now expose `isAuthenticatedBy(string $expectedToken): bool`, which
compares tokens with `hash_equals()`. Replace any `!==`/`===` token comparison in your listeners:

```php
// Before (timing-unsafe)
if ($event->getAuthenticationToken() !== $passbook->getAuthToken()) {
    $event->notAuthorized();
    return;
}

// After
if (!$event->isAuthenticatedBy($passbook->getAuthToken())) {
    $event->notAuthorized();
    return;
}
```

## Web service behavior

- The registration endpoint returns `400 Bad Request` for malformed JSON bodies before your
  listener runs.
- The `/v1/log` endpoint validates and caps the payload, and passes it to the logger as
  `['logs' => [...]]` context.
- `DeviceRequestUpdatedPassesEvent::getPassesUpdatedSince()` is populated from the
  `passesUpdatedSince` query parameter — use it to return only serial numbers updated since that
  time.
- `RetrieveUpdatedPassbookEvent::getUpdatedSince()` is populated from the `If-Modified-Since`
  header; setting `notFound()` on that event returns `404 Not Found`.

## Route configuration

If you were importing the bundle's routes in your application, update the import path:

```yaml
# Before (1.x)
apple_passbook:
    resource: "@ApplePassbookBundle/Controller"
    type: annotation

# After (2.0) — in routes.php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('@ApplePassbookBundle/Resources/config/routes.php');
};
```

Or in YAML:

```yaml
# After (2.0) — in routes.yaml
apple_passbook:
    resource: "@ApplePassbookBundle/Resources/config/routes.php"
```

The bundle now includes routes for all three controllers (DeviceController, PassbookController, and LogController) in the route configuration file.

## Push notifications (PassbookUpdateNotifier)

The `PassbookUpdateNotifier` has been moved to the core library as `LauLamanApps\ApplePassbook\Build\Notifier` and rewritten to use the modern HTTP/2 APNs API (the old binary protocol on port 2195 was shut down by Apple).

```php
// Before (1.x) — bundle class, non-functional
use LauLamanApps\ApplePassbookBundle\PushNotification\PassbookUpdateNotifier;

$notifier->send($pushToken);

// After (2.0) — core library class, uses HTTP/2 APNs
use LauLamanApps\ApplePassbook\Build\Notifier;

$notifier->notify($pushToken);
```

The service ID has changed:

| 1.x | 2.0 |
|---|---|
| `laulamanapps_apple_passbook.push_notification.update_notifier` | `laulamanapps_apple_passbook.build.notifier` |

The class alias for autowiring is now `LauLamanApps\ApplePassbook\Build\Notifier`.

## Service configuration

The bundle's service definitions have been converted from XML to PHP. Service ID changes:

| 1.x | 2.0 |
|---|---|
| `laulamanapps_apple_passbook.push_notification.update_notifier` | `laulamanapps_apple_passbook.build.notifier` |

All other service IDs remain the same:

- `laulamanapps_apple_passbook.build.compiler`
- `laulamanapps_apple_passbook.build.signer`
- `laulamanapps_apple_passbook.build.compressor`
- `laulamanapps_apple_passbook.build.manifestgenerator`

## Controllers

The controllers no longer extend `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`. If you were relying on this inheritance (e.g., for type hints), update accordingly. The controllers are now plain classes with constructor-injected dependencies.

## Bundle configuration

The `password` field is now optional (required for P12 certificates, not needed for PEM). A new `environment` option replaces the need for separate sandbox configuration:

```yaml
laulamanapps_apple_passbook:
    certificate: '%kernel.project_dir%/config/certificates/pass.p12'
    password: 'your-certificate-password'       # optional (required for P12)
    team_identifier: 'YOUR_TEAM_ID'             # optional
    pass_type_identifier: 'pass.com.example'    # optional
    environment: 'production'                   # optional, 'production' (default) or 'sandbox'
```
