[![Build Status](https://travis-ci.org/devhelp/piwik-bundle.svg?branch=master)](https://travis-ci.org/devhelp/piwik-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/devhelp/piwik-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/devhelp/piwik-bundle?branch=master)

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

This example uses PiwikGuzzleClient class that is responsible for making http request to Piwik.
You can include this extension by including [devhelp/piwik-api-guzzle](http://github.com/devhelp/piwik-api-guzzle) in your project

```yml
my_piwik.client:
    class: Devhelp\Piwik\Api\Guzzle\Client\PiwikGuzzleClient
    arguments:
        # guzzle service must implement GuzzleHttp\ClientInterface
        - @guzzle
```

### Use API method in your use case

service configuration

```yml
my_service:
    class: Acme\DemoBundle\Service\MyService
    arguments:
        # it is an alias of first configured api (in this case it equals devhelp_piwik.api.reader service)
        - @devhelp_piwik.api
```

service definition

```php
namespace Acme\DemoBundle\Service;


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
    class: Acme\DemoBundle\Param\MyTokenAuthProvider
    arguments:
        - @security.context
```

MyTokenAuthProvider class definition

```php
namespace Acme\DemoBundle\Param;


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
        return $this->securityContext->getToken()->getUser()->getPiwikToken();
    }
}
```

### Define API methods as services

```yml
my_piwik_method:
    class: Devhelp\Piwik\Api\Method\Method
    factory_service: devhelp_piwik.api
    factory_method: getMethod
    arguments:
        - VisitFrequency.get
```

## Feedback/Requests

Feel free to create an issue if you think that something is missing. Feedback is more than welcome!

## Credits

Brought to you by : [devhelp.pl](http://devhelp.pl)
