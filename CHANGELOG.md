# CHANGELOG

### 2.0.0

#### Added
- Support for Symfony 6.4, 7.x and 8.x
- `environment` configuration option (`production` / `sandbox`) for the APNs environment
- `isAuthenticatedBy()` on `DeviceRegisteredEvent`, `DeviceUnregisteredEvent` and `RetrieveUpdatedPassbookEvent` — a timing-safe (`hash_equals`) token comparison; README examples use it
- The `passesUpdatedSince` query parameter is parsed and passed to `DeviceRequestUpdatedPassesEvent`
- The `If-Modified-Since` request header is parsed and passed to `RetrieveUpdatedPassbookEvent`
- `Status::NotFound` on `RetrieveUpdatedPassbookEvent` returns `404 Not Found` from the pass endpoint (per Apple's spec)
- Configuration reference documentation and logging-endpoint documentation in the README
- `UPGRADE.md` migration guide for upgrading from 1.x to 2.0

#### Security
- The device registration endpoint validates the request body (`pushToken` must be a non-empty string) and returns `400 Bad Request` instead of crashing on malformed JSON
- The `/v1/log` endpoint validates the `{"logs": [...]}` payload, caps it at 50 entries of 4096 characters, and returns `400 Bad Request` on malformed bodies (prevents log flooding/injection and 500s from attacker-controlled input)
- `Authorization` header parsing requires the exact `ApplePass ` prefix and is null-safe

#### Changed
- **BC**: Require PHP 8.1 or higher (was `>=7.3`)
- **BC**: Require `laulamanapps/apple-passbook` 2.0 (was `^1.1`)
- **BC**: `Status` is now a native PHP backed enum instead of a `werkspot/enum` `AbstractEnum`; use `===` case comparison instead of `->isXxx()` methods and `->value` instead of `->getValue()`
- **BC**: `AbstractEvent::getStatus()` now returns a `Status` enum case
- **BC**: Route configuration moved to `@ApplePassbookBundle/Resources/config/routes.php` (was annotation-based `@ApplePassbookBundle/Controller`)
- **BC**: Controllers no longer extend `AbstractController`; they are plain classes with constructor-injected dependencies
- **BC**: `password` configuration option is now optional (required only for P12 certificates)
- Service definitions converted from XML to PHP configuration
- Push notifications now use Apple's HTTP/2 APNs API
- Upgrade PHPUnit to ^10.0|^11.0 and convert doc-comment metadata to attributes (PHPUnit 12 ready)

#### Removed
- **BC**: `werkspot/enum` dependency
- **BC**: `PassbookUpdateNotifier` — moved to the core library as `LauLamanApps\ApplePassbook\Build\Notifier` (service id `laulamanapps_apple_passbook.build.notifier`)
- **BC**: `Status` `->isXxx()` helper methods and `->getValue()`

### 1.0.0
- Initial release
- Symfony 5.1 support
- Controllers for Apple PassKit Web Service API (device registration, pass updates, logging)
- Event-driven architecture for handling PassKit requests
- Push notification support
- DI configuration for apple-passbook Compiler, Signer, and Compressor services
