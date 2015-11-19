<?php

namespace Devhelp\PiwikBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InsertParamsServices implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $apiParams = $container->getParameter('devhelp_piwik.api.parameters');

        foreach ($apiParams as $api => $params) {
            $apiDefinition = $container->getDefinition('devhelp_piwik.api.'.$api);

            $defaultParams = array();

            foreach ($params as $name => $value) {
                if ($container->has($value)) {
                    $definition = $container->findDefinition($value);

                    if (!is_a($definition->getClass(), '\Devhelp\Piwik\Api\Param\Param', true)) {
                        throw new \InvalidArgumentException('Param must implement Devhelp\Piwik\Api\Param\Param');
                    }

                    $defaultParams[$name] = new Reference($value);
                } else {
                    $defaultParams[$name] = $value;
                }
            }

            $apiDefinition->addMethodCall('setDefaultParams', array($defaultParams));
        }
    }
}
