[![Build Status](https://travis-ci.org/devhelp/piwik-bundle.svg?branch=master)](https://travis-ci.org/devhelp/piwik-bundle.svg)

## Installation

For more information please check [composer website](http://getcomposer.org).

```
$ composer require 'devhelp/piwik-bundle:dev-master'
```

## Purpose

Bundle provides integration with Piwik API. Adds services to the dependency injection container that allows to use Piwik API methods as services.
It uses [devhelp/piwik-api](http://github.com/devhelp/piwik-api) library - check its documentation for more advanced usage.

## Usage

### Define API connection in config.yml

```yml
devhelp_piwik:
    client: my_piwik.client
    api:
        reader:
            url: http://my_piwik_instance.piwik.pro
            default_params:
                token_auth: %piwik_token_auth%
                idSite: %piwik_site_id%
```

### Create piwik client service that is used in config.yml

This example uses built-in PiwikGuzzleClient class that is responsible for making http request to Piwik

```yml
my_piwik.client:
    class: Devhelp\Piwik\Api\Client\PiwikGuzzleClient
    arguments:
        # guzzle service must implement GuzzleHttp\ClientInterface
        - @guzzle
```

### Use API method in your use case

service configuration

```yml
my_service:
    class: Acme\Service\MyService
    arguments:
        # it is an alias of first configured api (in this case it equals devhelp_piwik.api.reader service)
        - @devhelp_piwik.api
```

service definition

```php
namespace Acme\Service;


use Devhelp\Piwik\Api\Api;

class MyService
{

    /**
     * @var Api
     */
    private $piwikApi;

    public function __construct(Api $piwikApi)
    {
        $this->piwikApi = $piwikApi;
    }

    public function doSomething()
    {
        //...
        $this->piwikApi->getMethod('PiwikPlugin.pluginAction')->call();
        //...
    }
}
```

### Define API parameters resolved at runtime

You are allowed to set services as a params. If you do that then the service will be used to resolve the parameter
at runtime. For example have a service that would return token_auth of logged in user


```yml
devhelp_piwik:
    client: my_piwik.client
    api:
        reader:
            url: http://my_piwik_instance.piwik.pro
            default_params:
                token_auth: my_token_auth_provider
                idSite: %piwik_site_id%
```

my_token_auth_provider service definition

```yml
my_token_auth_provider:
    class: Acme\Param\MyTokenAuthProvider
    arguments:
        - @security.context
```

MyTokenAuthProvider class definition

```php
namespace Acme\Param;


use Devhelp\Piwik\Api\Param\Param;

class MyTokenAuthProvider implements Param
{

    /**
     * @var SecurityContext
     */
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function value()
    {
        return array('token_auth' => $this->securityContext->getToken()->getUser()->getPiwikToken());
    }
}
```

### Define API methods as services

```yml
my_piwik_method:
    class: Devhelp\Piwik\Api\Method\Method
    factory_service: devhelp_piwik.api
    factory_method: getMethod
    arguments: 'PiwikPlugin.pluginAction'
```

## Credits

Brought to you by : Devhelp.pl (http://devhelp.pl)
