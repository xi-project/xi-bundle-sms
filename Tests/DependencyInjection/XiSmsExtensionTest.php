<?php

namespace Xi\Bundle\SmsBundle\Tests\DependencyInjection;

use Xi\Bundle\SmsBundle\DependencyInjection\XiSmsExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;
use Xi\Bundle\SmsBundle\DependencyInjection\Compiler\FilterPass;
use Symfony\Component\DependencyInjection\Definition;

class XiSmsExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');
        $this->container = new ContainerBuilder();
        $this->container->addScope(new Scope('request'));
        $this->container->register('request', 'Symfony\\Component\\HttpFoundation\\Request')->setScope('request');
        $this->container->setParameter('kernel.bundles', array());
        $this->container->setParameter('kernel.cache_dir', __DIR__);
        $this->container->setParameter('kernel.debug', false);
        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->setParameter('kernel.charset', 'UTF-8');
        $this->container->set('kernel', $this->kernel);

        $this->container->addCompilerPass(new FilterPass());

    }


    static public function assertSaneContainer(Container $container, $message = '')
    {
        $errors = array();
        foreach ($container->getServiceIds() as $id) {
            try {
                $container->get($id);
            } catch (\Exception $e) {
                $errors[$id] = $e->getMessage();
            }
        }

        self::assertEquals(array(), $errors, $message);
    }

    public function provideDebugModes()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider provideDebugModes
     * @test
     */
    public function defaultConfigShouldProvideSaneDefaults($debug)
    {
        $this->container->setParameter('kernel.debug', $debug);

        $extension = new XiSmsExtension();
        $extension->load(array(array()), $this->container);

        $this->assertSaneContainer($this->getDumpedContainer());

        $this->assertTrue($this->container->has('xi_sms.gateway.raw'));
        $this->assertTrue($this->container->has('xi_sms.gateway'));
        $this->assertTrue($this->container->has('xi_sms.filter.number_limiter'));
    }

    /**
     * @test
     * @dataProvider provideDebugModes
     */
    public function gatewayParamsShouldAcceptObjectReferences($debug)
    {
        $this->container->setParameter('kernel.debug', $debug);

        $mockClassName = $this->getMockClass('Xi\Sms\Gateway\MockGateway');
        $lusauttaja = new Definition($mockClassName);
        $this->container->setDefinition('lusauttaja', $lusauttaja);

        $extension = new XiSmsExtension();
        $extension->load(
            array(
                array(
                    'sms_gateway' => array(
                        'service_id' => 'lusauttaja',
                    ),
                )
            ),
            $this->container
        );

        $this->assertSaneContainer($this->getDumpedContainer());

        $this->assertTrue($this->container->has('xi_sms.gateway.raw'));
        $this->assertTrue($this->container->has('xi_sms.gateway'));
        $this->assertTrue($this->container->has('xi_sms.filter.number_limiter'));
    }



    private function getDumpedContainer()
    {
        static $i = 0;
        $class = 'SmsExtensionTestContainer'.$i++;

        $this->container->compile();

        $dumper = new PhpDumper($this->container);
        eval('?>'.$dumper->dump(array('class' => $class)));

        $container = new $class();
        $container->enterScope('request');
        $container->set('request', Request::create('/'));
        $container->set('kernel', $this->kernel);

        return $container;
    }
}
