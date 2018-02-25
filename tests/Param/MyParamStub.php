<?php

namespace Devhelp\PiwikBundle\Param;


use Devhelp\Piwik\Api\Param\Param;

class MyParamStub implements Param
{
    public function value()
    {
        return ['stub_param' => 'value'];
    }
}
