<?php

namespace Xi\Bundle\SmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Xi\Bundle\SmsBundle\DependencyInjection\Compiler\FilterPass;

class XiSmsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FilterPass());
    }
}
