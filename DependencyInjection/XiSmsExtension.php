<?php

namespace Xi\Bundle\SmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Reference;

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

        $definition = new Definition($config['sms_gateway']['class'], $config['sms_gateway']['arguments']);
        $container->setDefinition('xi_sms.sms_gateway.raw', $definition);

        $definition = new Definition(
            'Xi\Sms\Gateway\Filter\NumberLimitingFilter',
            array(
                $config['sms_gateway']['number_limiter']['whitelist'],
                $config['sms_gateway']['number_limiter']['blacklist']
            )
        );
        $container->setDefinition('xi_sms.filter.number_limiter', $definition);

        $definition = new Definition(
            'Xi\Sms\Gateway\FilterGateway',
            array(
                new Reference('xi_sms.sms_gateway.raw'),
                array(
                    new Reference('xi_sms.filter.number_limiter')
                )

            )
        );
        $container->setDefinition('xi_sms.sms_gateway', $definition);
    }
}
