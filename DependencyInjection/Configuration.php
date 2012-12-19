<?php

namespace Xi\Bundle\SmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('xi_sms');

        $rootNode
            ->children()
            ->arrayNode('sms_gateway')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('service_id')
            ->defaultValue(null)
            ->end()
            ->scalarNode('class')
            ->defaultValue('Xi\Sms\Gateway\MockGateway')
            ->treatNullLike('Xi\Sms\Gateway\MockGateway')
            ->end()
            ->arrayNode('number_limiter')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('whitelist')
            ->isRequired()
            ->treatNullLike(array())
            ->prototype('scalar')
            ->end()
            ->end()
            ->arrayNode('blacklist')
            ->isRequired()
            ->treatNullLike(array())
            ->prototype('scalar')
            ->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('arguments')
            ->treatNullLike(array())
            ->prototype('scalar')
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
