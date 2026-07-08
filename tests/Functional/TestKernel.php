<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\Tests\Functional;

use LauLamanApps\ApplePassbookBundle\ApplePassbookBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): iterable
    {
        return [
            new ApplePassbookBundle(),
            new FrameworkBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__ . '/../../src/Resources/config/routes.php');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'F00',
            'test' => true,
            'http_method_override' => false,
        ]);

        $containerBuilder->loadFromExtension('laulamanapps_apple_passbook', [
            'certificate' => '/tmp/certificate.p12',
            'password' => '132456',
        ]);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/apple_passbook_bundle_test/' . spl_object_hash($this);
    }
}
