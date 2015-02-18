<?php

namespace Devhelp\PiwikBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InsertParamsServices implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $apiParams = $container->getParameter('devhelp.piwik_api.parameters');

        if (!$apiParams) {
            return;
        }

        foreach ($apiParams as $api => $params) {
            $apiDefinition = $container->getDefinition('devhelp.piwik_api.'.$api);

            $defaultParams = array();

            foreach ($params as $name => $value) {
                if (is_integer($name) && $container->findDefinition($value)) {
                    $defaultParams[] = new Reference($value);
                } else {
                    $defaultParams[$name] = $value;
                }
            }

            $apiDefinition->addMethodCall('setDefaultParams', $defaultParams);
        }
    }
}
