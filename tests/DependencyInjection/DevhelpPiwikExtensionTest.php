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

    protected function setUp()
    {
        $this->extension = new DevhelpPiwikExtension();

        $this->container = new ContainerBuilder();
    }

    /** @test */
    public function it_does_not_require_anything_to_be_configured_at_start()
    {
        $this->extension->load(array(array()), $this->container);
    }

    /** @test */
    public function it_adds_each_api_service_to_the_container()
    {
        $configuration = array(
            'client' => 'my.client.service',
            'api' => array(
                'reader' => array(
                    'url' => 'http://reader.piwik.tld',
                ),
                'writer' => array(
                    'url' => 'http://writer.piwik.tld',
                ),
            ),
        );

        $this->extension->load(array($configuration), $this->container);

        $this->assertTrue($this->container->hasDefinition('devhelp_piwik.api.reader'));
        $this->assertTrue($this->container->hasDefinition('devhelp_piwik.api.writer'));
    }

    /** @test */
    public function it_aliases_first_api_service_as_default()
    {
        $configuration = array(
            'client' => 'my.client.service',
            'api' => array(
                'reader' => array(
                    'url' => 'http://reader.piwik.tld',
                ),
                'writer' => array(
                    'url' => 'http://writer.piwik.tld',
                ),
            ),
        );

        $this->extension->load(array($configuration), $this->container);

        $this->assertSame('devhelp_piwik.api.reader', (string) $this->container->getAlias('devhelp_piwik.api'));
        $this->assertSame('devhelp_piwik.api.reader', (string) $this->container->getAlias('devhelp_piwik.api.default'));
    }

    /** @test */
    public function it_adds_parameter_with_client_service_id()
    {
        $configuration = array(
            'client' => 'my.client.service',
            'api' => array(
                'reader' => array(
                    'url' => 'http://reader.piwik.tld',
                )
            ),
        );

        $this->extension->load(array($configuration), $this->container);

        $this->assertSame('my.client.service', $this->container->getParameter('devhelp_piwik.client.service_id'));
    }

    /** @test */
    public function it_adds_parameter_with_api_parameters()
    {
        $configuration = array(
            'client' => 'my.client.service',
            'api' => array(
                'reader' => array(
                    'url' => 'http://reader.piwik.tld',
                )
            ),
        );

        $params = array(
            'param1' => 'value',
            'param2' => 'param_service_id'
        );

        $configuration['api']['reader']['default_params'] = $params;

        $this->extension->load(array($configuration), $this->container);

        $this->assertSame(array('reader' => $params), $this->container->getParameter('devhelp_piwik.api.parameters'));
    }
}
