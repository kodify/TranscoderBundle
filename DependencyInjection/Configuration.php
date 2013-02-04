<?php

namespace Kodify\TranscoderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kodify_transcoder');

        // Test data, production should have different ones
        $rootNode
            ->children()
            ->scalarNode('driver')->end()
            ->scalarNode('pandastream_access_key')->end()
            ->scalarNode('pandastream_secret_key')->end()
            ->scalarNode('pandastream_api_url')->end()
            ->scalarNode('pandastream_cloud_id')->end()
            ->end();
        return $treeBuilder;
    }
}
