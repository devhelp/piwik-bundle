<?php

namespace Devhelp\PiwikBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AddPiwikClientDefinitionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var AddPiwikClientDefinition
     */
    private $compilerPass;

    /**
     * @var string
     */
    private $serviceId;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->compilerPass = new AddPiwikClientDefinition();
    }

    /**
     * @test
     */
    public function it_aliases_client_service_definition()
    {
        $this->given_piwik_client_was_set();
        $this->given_custom_piwik_client_service_exists();
        $this->when_compiler_is_processed();
        $this->then_the_service_is_aliased_as_piwik_client();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_if_there_is_not_service_defined_as_a_client()
    {
        $this->given_the_service_does_not_exist();
        $this->when_compiler_is_processed();
    }

    private function given_piwik_client_was_set()
    {
        $this->serviceId = 'my.piwik.client';
        $this->container->setParameter('devhelp_piwik.client.service_id', $this->serviceId);
    }

    private function given_custom_piwik_client_service_exists()
    {
        $this->container->setDefinition($this->serviceId, new Definition('Foo\Class'));
    }

    private function given_the_service_does_not_exist()
    {
        $this->container->removeDefinition($this->serviceId);
    }

    private function when_compiler_is_processed()
    {
        $this->compilerPass->process($this->container);
    }

    private function then_the_service_is_aliased_as_piwik_client()
    {
        $aliasedDefinition = $this->container->findDefinition('devhelp_piwik.client');
        $originalDefinition = $this->container->getDefinition($this->serviceId);

        $this->assertSame($aliasedDefinition, $originalDefinition);
    }
}
