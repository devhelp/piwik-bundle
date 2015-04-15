<?php

namespace Devhelp\PiwikBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddPiwikClientDefinition implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $clientServiceId = $container->getParameter('devhelp_piwik.client.service_id');

        if (!$container->hasDefinition($clientServiceId)) {
            throw new \InvalidArgumentException("$clientServiceId service does not exists");
        }

        $container->setAlias('devhelp_piwik.client', $clientServiceId);
    }
}
