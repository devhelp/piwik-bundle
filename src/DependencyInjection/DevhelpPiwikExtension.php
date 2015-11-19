<?php

namespace Devhelp\PiwikBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DevhelpPiwikExtension extends Extension
{

    /**
     * @var array
     */
    protected $config;

    /**
     * params to be injected as default (per client)
     *
     * @var array
     */
    private $apiParams = array();

    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $configuration = new Configuration();

        $this->config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('devhelp_piwik.client.service_id', $this->config['client']);

        $this->addApis();

        $container->setParameter('devhelp_piwik.api.parameters', $this->apiParams);
    }

    private function addApis()
    {
        $firstApiName = key($this->config['api']);

        foreach ($this->config['api'] as $name => $definition) {
            $this->addApi($name, $definition);
        }

        $this->container->setAlias('devhelp_piwik.api', 'devhelp_piwik.api.'.$firstApiName);
        $this->container->setAlias('devhelp_piwik.api.default', 'devhelp_piwik.api.'.$firstApiName);
    }

    private function addApi($name, $data)
    {
        $apiDefinition = new Definition('Devhelp\Piwik\Api\Api', array(
            new Reference('devhelp_piwik.client'),
            $data['url']
        ));

        $this->apiParams[$name] = $data['default_params'];

        $this->container->setDefinition('devhelp_piwik.api.'.$name, $apiDefinition);
    }
}
