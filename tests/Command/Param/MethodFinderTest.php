<?php


namespace Devhelp\PiwikBundle\Command\Param;


class MethodFinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var MethodFinder
     */
    private $methodFinder;

    private $result;

    private $methodService;

    private $apiService;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->methodFinder = new MethodFinder($this->container);
    }

    /**
     * @test
     */
    public function it_returns_method_service()
    {
        $this->given_container_has_method_service();
        $this->when_find_is_called();
        $this->then_method_is_returned_from_the_service();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_if_matched_service_is_not_an_instance_of_Method()
    {
        $this->given_container_has_no_method_service();
        $this->when_find_is_called();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_exception_if_api_service_does_not_exist()
    {
        $this->given_container_does_not_have_method_service();
        $this->given_container_does_not_have_api_service();
        $this->when_find_is_called();
    }

    /**
     * @test
     */
    public function it_returns_method_using_Api()
    {
        $this->given_container_does_not_have_method_service();
        $this->given_container_has_api_service();
        $this->when_find_is_called();
        $this->then_method_is_returned_from_the_service();
    }

    private function given_container_has_method_service()
    {
        $this->container
            ->expects($this->any())
            ->method('has')
            ->with('method_arg')
            ->willReturn(true);

        $this->methodService = $this->getMockBuilder('Devhelp\Piwik\Api\Method\Method')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('method_arg')
            ->willReturn($this->methodService);
    }

    private function given_container_does_not_have_method_service()
    {
        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('method_arg')
            ->willReturn(false);
    }

    private function given_container_has_no_method_service()
    {
        $this->container
            ->expects($this->any())
            ->method('has')
            ->with('method_arg')
            ->willReturn(true);

        $this->methodService = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('method_arg')
            ->willReturn($this->methodService);
    }

    private function given_container_has_api_service()
    {
        $this->container
            ->expects($this->at(1))
            ->method('has')
            ->with('devhelp_piwik.api.api_opt')
            ->willReturn(true);

        $this->methodService = $this->getMockBuilder('Devhelp\Piwik\Api\Method\Method')
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiService = $this->getMockBuilder('Devhelp\Piwik\Api\Api')
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiService
            ->expects($this->any())
            ->method('getMethod')
            ->willReturn($this->methodService);

        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('devhelp_piwik.api.api_opt')
            ->willReturn($this->apiService);
    }

    private function given_container_does_not_have_api_service()
    {
        $this->container
            ->expects($this->at(1))
            ->method('has')
            ->with('devhelp_piwik.api.api_opt')
            ->willReturn(false);
    }

    private function when_find_is_called()
    {
        $this->result = $this->methodFinder->find('method_arg', 'api_opt');
    }

    private function then_method_is_returned_from_the_service()
    {
        $this->assertSame($this->methodService, $this->result);
    }
}
