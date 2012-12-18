<?php

namespace Xi\Bundle\SmsBundle\Tests;

use Xi\Bundle\SmsBundle\XiSmsBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Xi\Bundle\SmsBundle\DependencyInjection\Compiler\FilterPass;

class XiSmsBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function buildshouldAddCompilePass()
    {
        $bundle = new XiSmsBundle();

        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $containerBuilder
            ->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Xi\Bundle\SmsBundle\DependencyInjection\Compiler\FilterPass'));

        $bundle->build($containerBuilder);
    }
}
