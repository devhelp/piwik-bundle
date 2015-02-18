<?php

namespace Devhelp\PiwikBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;

class DevhelpPiwikExtensionIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DevhelpPiwikExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    private $minConfiguration;

    protected function setUp()
    {
        $this->extension = new DevhelpPiwikExtension();

        $this->container = new ContainerBuilder();

        $this->minConfiguration = array(
            'client' => 'my.client.service',
            'api' => array(
                'frontend' => array(
                    'url' => 'http://frontend.piwik.tld',
                ),
            ),
        );
    }

    /**
     * @test
     */
    public function it_adds_api_services_to_the_container()
    {
        $configuration = $this->minConfiguration;

        $this->extension->load(array($configuration), $this->container);

        $apiServiceId = 'devhelp.piwik_api.frontend';

        /**
         * has definition for the registered instance
         */
        $this->assertTrue($this->container->hasDefinition($apiServiceId));

        /**
         * aliases default api (first registered)
         */
        $this->assertSame($apiServiceId, (string) $this->container->getAlias('devhelp.piwik_api'));

        /**
         * aliases default api as well
         */
        $this->assertSame($apiServiceId, (string) $this->container->getAlias('devhelp.piwik_api.default'));
    }

    /**
     * @test
     */
    public function it_adds_parameter_with_client_service_id()
    {
        $configuration = $this->minConfiguration;

        $this->extension->load(array($configuration), $this->container);

        $this->assertSame('my.client.service', $this->container->getParameter('devhelp.piwik_client.service_id'));
    }

    /**
     * @test
     */
    public function it_adds_parameter_with_api_parameters()
    {
        $configuration = $this->minConfiguration;

        $params = array(
            'param1' => 'value',
            'param_service_id'
        );

        $configuration['api']['frontend']['default_params'] = $params;

        $this->extension->load(array($configuration), $this->container);

        $this->assertSame(array('frontend' => $params), $this->container->getParameter('devhelp.piwik_api.parameters'));
    }
}
