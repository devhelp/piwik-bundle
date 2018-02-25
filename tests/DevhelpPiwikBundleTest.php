<?php

namespace Devhelp\PiwikBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class DevhelpPiwikBundleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DevhelpPiwikBundle
     */
    private $bundle;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->bundle = new DevhelpPiwikBundle();
    }

    /** @test */
    public function it_adds_compiler_passes_to_the_container()
    {
        $this->when_bundle_is_built();
        $this->then_compiler_passes_are_added();
    }

    private function when_bundle_is_built()
    {
        $this->bundle->build($this->container);
    }

    private function then_compiler_passes_are_added()
    {
        $passes = $this->container->getCompiler()->getPassConfig()->getBeforeOptimizationPasses();

        $classes = array_map(function ($pass) {
            return get_class($pass);
        }, $passes);

        $this->assertContains('Devhelp\PiwikBundle\DependencyInjection\Compiler\AddPiwikClientDefinition', $classes);
		$this->assertContains('Devhelp\PiwikBundle\DependencyInjection\Compiler\InsertParamsServices', $classes);
    }
}
