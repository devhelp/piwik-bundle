<?php

namespace Devhelp\PiwikBundle\DependencyInjection\Command\Param;

use Devhelp\Piwik\Api\Api;
use Devhelp\Piwik\Api\Method\Method;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Log\NullLogger;

class MethodFinder
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->logger = $logger ? $logger : new NullLogger();
    }

    /**
     * @param $methodArg
     * @param $apiName
     * @return Method
     */
    public function find($methodArg, $apiName = null)
    {
        if ($this->container->has($methodArg)) {
            $method = $this->container->get($methodArg);

            $this->logger->debug("$methodArg resolved to a service");

            if ($apiName) {
                $this->logger->warning("'$apiName' api name will be ignored");
            }

        } else {
            $api = $this->getApi($apiName);

            $method = $api->getMethod($methodArg);

            $this->logger->debug("$methodArg resolved to a method name");
        }

        $this->checkIsInstanceOfMethod($methodArg, $method);

        return $method;
    }

    /**
     * @param $apiName
     * @return Api
     */
    private function getApi($apiName)
    {
        $apiServiceId = $this->getApiServiceId($apiName);

        if (!$this->container->has($apiServiceId)) {
            throw new \InvalidArgumentException("'$apiName' api service does not exist");
        }

        return $this->container->get($apiServiceId);
    }

    /**
     * @param $apiName
     * @return string
     */
    private function getApiServiceId($apiName)
    {
        $serviceId = 'devhelp_piwik.api';

        if ($apiName) {
            $serviceId .= '.'.$apiName;
        }

        return $serviceId;
    }

    /**
     * @param $methodArg
     * @param $method
     */
    private function checkIsInstanceOfMethod($methodArg, $method)
    {
        if (!$method instanceof Method) {
            throw new \InvalidArgumentException(
                sprintf(
                    "'%s' object is of invalid class, expected Devhelp\\Piwik\\Api\\Method\\Method, got %s",
                    $methodArg,
                    get_class($method)
                )
            );
        }
    }
}
