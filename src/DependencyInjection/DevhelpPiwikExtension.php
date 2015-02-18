<?php

namespace Devhelp\PiwikBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class DevhelpPiwikExtension extends Extension
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Loader\FileLoader;
     */
    protected $loader;

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

        $container->setParameter('devhelp.piwik_client.service_id', $this->config['client']);

        $this->addApis();

        $container->setParameter('devhelp.piwik_api.parameters', $this->apiParams);
    }

    private function addApis()
    {
        foreach ($this->config['api'] as $name => $definition) {
            $this->addApi($name, $definition);
        }

        $firstApiName = array_keys($this->config['api'])[0];

        $this->container->setAlias('devhelp.piwik_api', 'devhelp.piwik_api.'.$firstApiName);
        $this->container->setAlias('devhelp.piwik_api.default', 'devhelp.piwik_api.'.$firstApiName);
    }

    private function addApi($name, $data)
    {
        $apiDefinition = new Definition('Devhelp\Piwik\Api\Api', array(
            new Reference('devhelp.piwik_client'),
            $data['url']
        ));

        $this->apiParams[$name] = $data['default_params'];

        $this->container->setDefinition('devhelp.piwik_api.'.$name, $apiDefinition);
    }
}
