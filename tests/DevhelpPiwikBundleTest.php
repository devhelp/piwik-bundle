<?php

namespace Devhelp\PiwikBundle;


class DevhelpPiwikBundleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DevhelpPiwikBundle
     */
    private $bundle;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    protected function setUp()
    {
        $this->container = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->bundle = new DevhelpPiwikBundle();
    }

    /**
     * @test
     */
    public function it_adds_compiler_passes()
    {
        $this->it_adds_compiler_passes_to_the_container();
        $this->when_bundle_is_built();
    }

    private function when_bundle_is_built()
    {
        $this->bundle->build($this->container);
    }

    private function it_adds_compiler_passes_to_the_container()
    {
        $this->container
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->callback(function ($object) {
                return get_class($object) == 'Devhelp\PiwikBundle\DependencyInjection\Compiler\AddPiwikClientDefinition';
            }));

        $this->container
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->callback(function ($object) {
                return get_class($object) == 'Devhelp\PiwikBundle\DependencyInjection\Compiler\InsertParamsServices';
            }));
    }
}
