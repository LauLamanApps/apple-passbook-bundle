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
use Symfony\Component\Routing\RouteCollectionBuilder;

class TestKernel  extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): array
    {
        return [
            new ApplePassbookBundle(),
            new FrameworkBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes)
    {
        $routes->import(__DIR__.'/../../src/Resources/config/routes.xml');
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'F00',
            'test' => true,
        ]);

        $containerBuilder->loadFromExtension('laulamanapps_apple_passbook', [
            'certificate' => '/Users/laulaman/Projects/LauLamanApps/OpenSource/apple-passbook/certificates/certificate.p12',
            'password' => '132456',
        ]);
    }

//    public function getCacheDir()
//    {
//        return __DIR__.'/../../cache/'.spl_object_hash($this);
//    }
}
