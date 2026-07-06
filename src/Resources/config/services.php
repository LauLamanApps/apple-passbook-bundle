<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LauLamanApps\ApplePassbook\Build\Compiler;
use LauLamanApps\ApplePassbook\Build\Compressor;
use LauLamanApps\ApplePassbook\Build\ManifestGenerator;
use LauLamanApps\ApplePassbook\Build\Notifier;
use LauLamanApps\ApplePassbook\Build\Signer;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\DeviceController;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\LogController;
use LauLamanApps\ApplePassbookBundle\Controller\V1\PassKit\PassbookController;
use ZipArchive;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('laulamanapps_apple_passbook.build.compiler', Compiler::class)
        ->public()
        ->args([
            service('laulamanapps_apple_passbook.build.manifestgenerator'),
            service('laulamanapps_apple_passbook.build.signer'),
            service('laulamanapps_apple_passbook.build.compressor'),
        ]);

    $services->set('laulamanapps_apple_passbook.build.manifestgenerator', ManifestGenerator::class);

    $services->set('laulamanapps_apple_passbook.build.signer', Signer::class);

    $services->set('laulamanapps_apple_passbook.php.zip_archive', ZipArchive::class);

    $services->set('laulamanapps_apple_passbook.build.compressor', Compressor::class)
        ->args([
            service('laulamanapps_apple_passbook.php.zip_archive'),
        ]);

    $services->set('laulamanapps_apple_passbook.build.notifier', Notifier::class)
        ->public();

    $services->set(DeviceController::class)
        ->autowire()
        ->public()
    ;

    $services->set(PassbookController::class)
        ->autowire()
        ->public()
    ;

    $services->set(LogController::class)
        ->autowire()
        ->public()
    ;

    $services->alias(Compiler::class, 'laulamanapps_apple_passbook.build.compiler');
    $services->alias(Notifier::class, 'laulamanapps_apple_passbook.build.notifier');
};
