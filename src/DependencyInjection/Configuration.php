<?php

declare(strict_types=1);

namespace LauLamanApps\ApplePassbookBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const ROOT = 'laulamanapps_apple_passbook';
    
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT);
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()->scalarNode('certificate')->isRequired()->end();
        $rootNode->children()->scalarNode('password')->isRequired()->end();
        $rootNode->children()->scalarNode('team_identifier')->defaultNull()->end();
        $rootNode->children()->scalarNode('pass_type_identifier')->defaultNull()->end();

        return $treeBuilder;
    }
}
