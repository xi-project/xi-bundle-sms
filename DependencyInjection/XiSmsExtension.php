<?php

namespace Xi\Bundle\SmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Alias;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class XiSmsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['sms_gateway']['service_id']) {
            $alias = new Alias('xi_sms.gateway.raw');
            $container->setAlias($alias, $config['sms_gateway']['service_id']);
        } else {
            $definition = new Definition($config['sms_gateway']['class'], $config['sms_gateway']['arguments']);
            $container->setDefinition('xi_sms.gateway.raw', $definition);
        }

        $numberFilter = $container->getDefinition('xi_sms.filter.number_limiter');
        $numberFilter->addArgument($config['sms_gateway']['number_limiter']['whitelist']);
        $numberFilter->addArgument($config['sms_gateway']['number_limiter']['blacklist']);
    }
}
