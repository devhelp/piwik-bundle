[![Build Status](https://travis-ci.org/devhelp/piwik-bundle.svg?branch=master)](https://travis-ci.org/devhelp/piwik-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/devhelp/piwik-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/devhelp/piwik-bundle?branch=master)

## Installation

For more information please check [composer website](http://getcomposer.org).

```
$ composer require 'devhelp/piwik-bundle:dev-master'
```

Add the bundle to `AppKernel`

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            //...
            new \Devhelp\PiwikBundle\DevhelpPiwikBundle(),
            //...
        );

        //...

        return $bundles;
    }

    //...
}
```

Full working example can be found at [devhelp/piwik-bundle-sandbox](http://github.com/devhelp/piwik-bundle-sandbox)

## Purpose

Bundle provides integration with [Piwik API](http://developer.piwik.org/api-reference/reporting-api). Adds services to the dependency injection container that allows to use Piwik API methods as services.
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

This example uses `PiwikGuzzleClient` class that is responsible for making http request to [Piwik](http://piwik.org).
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
at runtime. For example have a service that would return `token_auth` of logged in user


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

`my_token_auth_provider` service definition

```yml
my_token_auth_provider:
    class: Acme\DemoBundle\Param\MyTokenAuthProvider
    arguments:
        - @security.token_storage
```

`MyTokenAuthProvider` class definition (assumes that User class has getPiwikToken method)

```php
namespace Acme\DemoBundle\Param;

use Devhelp\Piwik\Api\Param\Param;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class MyTokenAuthProvider implements Param
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function value()
    {
        $token = $this->tokenStorage->getToken();

        return $token instanceof TokenInterface ? $token->getUser()->getPiwikToken() : null;
    }
}
```

### Define API methods as services

```yml
my_piwik_method:
    class: Devhelp\Piwik\Api\Method\Method
    factory:
        - @devhelp_piwik.api
        - getMethod
    arguments:
        - VisitFrequency.get
```

This depends on your Symfony version (check [here](http://symfony.com/doc/current/components/dependency_injection/factories.html))

### Calling API using Symfony command

`devhelp_piwik:api:call` command allows you to call the API from command line. You can do it either by specifying method service id
or by passing method name together with api name (or use the default)

for more information please run

```
$ console devhelp_piwik:api:call --help
```

## Feedback/Requests

Feel free to create an issue if you think that something is missing or needs fixing. Feedback is more than welcome!

## Credits

Brought to you by : [devhelp.pl](http://devhelp.pl)
