<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\DependencyInjection;

use LauLamanApps\ApplePassbook\Build\ApnsEnvironment;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class ApplePassbookExtension extends Extension
{
    public function getAlias(): string
    {
        return Configuration::ROOT;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('laulamanapps_apple_passbook.build.signer');
        $definition->setArgument(0, $config['certificate']);
        $definition->setArgument(1, $config['password']);

        $definition = $container->getDefinition('laulamanapps_apple_passbook.build.compiler');
        $definition->setArgument(3, $config['pass_type_identifier']);
        $definition->setArgument(4, $config['team_identifier']);

        $environment = match ($config['environment']) {
            'sandbox' => ApnsEnvironment::Sandbox,
            default => ApnsEnvironment::Production,
        };

        $definition = $container->getDefinition('laulamanapps_apple_passbook.build.notifier');
        $definition->setArgument(0, $config['certificate']);
        $definition->setArgument(1, $config['password']);
        $definition->setArgument(2, $environment);
    }
}
