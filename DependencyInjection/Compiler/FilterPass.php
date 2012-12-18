<?php

namespace Xi\Bundle\SmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
* Registers filters to filtering gateway decorator
*/
class FilterPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('xi_sms.filter');
        $gateway = $container->getDefinition('xi_sms.gateway');

        foreach ($services as $service => $params) {
            $gateway->addMethodCall('addFilter', array(new Reference($service)));
        }
    }
}
