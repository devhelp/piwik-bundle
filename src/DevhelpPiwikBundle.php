<?php

namespace Devhelp\PiwikBundle;

use Devhelp\PiwikBundle\DependencyInjection\Compiler\AddPiwikClientDefinition;
use Devhelp\PiwikBundle\DependencyInjection\Compiler\InsertParamsServices;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DevhelpPiwikBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddPiwikClientDefinition());
        $container->addCompilerPass(new InsertParamsServices());
    }
}
