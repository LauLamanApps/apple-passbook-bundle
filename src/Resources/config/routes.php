<?php

declare(strict_types=1);

use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('laulamanapps_apple_passbook_bundle.device.register', '/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}/{serialNumber}')
        ->controller([DeviceController::class, 'register'])
        ->methods(['POST']);

    $routes->add('laulamanapps_apple_passbook_bundle.device.unregister', '/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}/{serialNumber}')
        ->controller([DeviceController::class, 'unregister'])
        ->methods(['DELETE']);

    $routes->add('laulamanapps_apple_passbook_bundle.device.updated_passes', '/v1/devices/{deviceLibraryIdentifier}/registrations/{passTypeIdentifier}')
        ->controller([DeviceController::class, 'getSerialNumbers'])
        ->methods(['GET']);

    $routes->add('laulamanapps_apple_passbook_bundle.passbook.get_updated', '/v1/passes/{passTypeIdentifier}/{serialNumber}')
        ->controller([PassbookController::class, 'getUpdatedPassbook'])
        ->methods(['GET']);

    $routes->add('laulamanapps_apple_passbook_bundle.log', '/v1/log')
        ->controller([LogController::class, 'log'])
        ->methods(['POST']);
};
