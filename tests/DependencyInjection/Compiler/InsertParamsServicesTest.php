<?php

namespace Devhelp\PiwikBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class InsertParamsServicesIntegrationTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var InsertParamsServices
     */
    private $compilerPass;

    private $params;

    private $paramServiceId;

    /**
     * @var Definition
     */
    private $apiDefinition;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();

        $this->apiDefinition = new Definition('Devhelp\Piwik\Api\Api');

        $this->container->setDefinition('devhelp_piwik.api.my_api', $this->apiDefinition);

        $this->compilerPass = new InsertParamsServices();
        $this->params = array('my_api' => array('my_param' => 'my_value'));
        $this->paramServiceId = 'my_param_service';
    }

    /**
     * @test
     */
    public function it_replaces_params_with_services()
    {
        $this->given_params_are_referencing_to_services();
        $this->when_compiler_is_processed();
        $this->then_references_are_replaced_with_services();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_if_param_resolver_is_of_invalid_class()
    {
        $this->given_param_is_referencing_to_service_that_is_of_invalid_class();
        $this->when_compiler_is_processed();
    }

    private function given_params_are_referencing_to_services()
    {
        $this->container->setDefinition($this->paramServiceId, new Definition('Devhelp\PiwikBundle\Param\MyParamStub'));

        $this->params['my_api'][0] = $this->paramServiceId;
    }

    private function given_param_is_referencing_to_service_that_is_of_invalid_class()
    {
        $this->container->setDefinition($this->paramServiceId, new Definition('Devhelp\PiwikBundle\Param\InvalidParamStub'));

        $this->params['my_api'][0] = $this->paramServiceId;
    }

    private function when_compiler_is_processed()
    {
        $this->container->setParameter('devhelp_piwik.api.parameters', $this->params);
        $this->compilerPass->process($this->container);
    }

    private function then_references_are_replaced_with_services()
    {
        $methodCalls = $this->apiDefinition->getMethodCalls();
        $defaultParams = $methodCalls[0][1];

        $this->assertSame('my_value', $defaultParams['my_param']);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $defaultParams[0]);
        $this->assertSame('my_param_service', (string) $defaultParams[0]);
    }
}
