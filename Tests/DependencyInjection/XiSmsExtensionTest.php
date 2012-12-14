<?php

namespace Xi\Bundle\SmsBundle\Tests\DependencyInjection;

use Xi\Bundle\SmsBundle\DependencyInjection\XiSmsExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

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
     */
    public function testDefaultConfig($debug)
    {
        $this->container->setParameter('kernel.debug', $debug);

        $extension = new XiSmsExtension();
        $extension->load(array(array()), $this->container);

        $this->assertTrue($this->container->has('svt_main.sms_gateway.raw'));
        $this->assertTrue($this->container->has('svt_main.sms_gateway'));

        $this->assertSaneContainer($this->getDumpedContainer());
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
