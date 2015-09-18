<?php


namespace Devhelp\PiwikBundle\DependencyInjection\Command;

use Devhelp\Piwik\Api\Method\Method;
use Devhelp\PiwikBundle\DependencyInjection\Command\Param\MethodFinder;
use Devhelp\PiwikBundle\DependencyInjection\Command\Param\ParamCombine;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class ApiCallCommand extends ContainerAwareCommand
{

    /**
     * @var ConsoleLogger
     */
    private $logger;

    protected function configure()
    {
        // @codingStandardsIgnoreStart
        $this
            ->setName('devhelp_piwik:api:call')
            ->setDescription('Calls piwik api method')
            ->addArgument(
                'method',
                InputArgument::REQUIRED,
                'Method to call. Can be either a service id (without @) or method name (like "Actions.getPageUrls")'
            )
            ->addOption(
                'params',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Method parameters as string (must be valid for parse_str function). '.
                'They will be merged with params from method service (if it has any) and will override existing ones if names are the same'
            )
            ->addOption(
                'api',
                null,
                InputOption::VALUE_OPTIONAL,
                'Api that will be used to call the method (name from configuration). '.
                'If not specified default api is used. Is ignored if "method" is passed as a service'
            )->addOption(
                'show-response',
                'sr',
                InputOption::VALUE_NONE,
                'If set then response is logged (if response is an object it must have toArray() or __toString() method defined!). '.
                'Verbosity level must be at least -vv and used together with this option in order to display the response'
            );
        // @codingStandardsIgnoreEnd
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $methodArg = $input->getArgument('method');
        $apiOption = $input->getOption('api');

        $params = [];

        if ($input->hasOption('params')) {
            parse_str($input->getOption('params'), $params);
        }

        $this->logger = new ConsoleLogger($output);

        $method = $this->getMethod($methodArg, $apiOption);

        $this->logger->info("Calling api...");
        $this->logger->info("- method: ".$method->name());
        $this->logger->info("- url: ".$method->url());

        if ($params) {
            $this->logger->info("- extra params: ");
            $this->logger->info(var_export($params, true));
        }

        $result = $method->call($params);

        $this->logger->info('Finished');

        if ($result && $input->hasOption('show-response')) {
            $this->showResponse($result);
        }
    }

    /**
     * @param $methodArg
     * @param $apiOption
     * @return Method
     */
    private function getMethod($methodArg, $apiOption)
    {
        $methodFinder = new MethodFinder($this->getContainer(), $this->logger);

        return $methodFinder->find($methodArg, $apiOption);
    }

    /**
     * @param $result
     */
    protected function showResponse($result)
    {
        $this->logger->info('Result: ');
        //duck-typing, mea culpa, improvement is on TODO list
        if (is_object($result)) {
            if (method_exists($result, 'toArray')) {
                $this->logger->info(var_export($result->toArray(), true));
            } else {
                $this->logger->info((string)$result);
            }
        } else {
            $this->logger->info(var_export($result, true));
        }
    }
}
