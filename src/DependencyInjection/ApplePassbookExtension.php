<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class ApplePassbookExtension extends Extension
{
    public function getAlias()
    {
        return Configuration::ROOT;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('laulamanapps_apple_passbook.build.signer');
        $definition->setArgument(0, $config['certificate']);
        $definition->setArgument(1, $config['password']);

        $definition = $container->getDefinition('laulamanapps_apple_passbook.build.compiler');
        $definition->setArgument(3, $config['pass_type_identifier']);
        $definition->setArgument(4, $config['team_identifier']);
    }
}
