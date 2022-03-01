<?php

namespace MonologCreator;

use MonologCreator;
use Monolog;

/**
 * Factory class to for creating monolog loggers with preconfigurated array
 */
class Factory
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @var array
     */
    private $levels = array(
        'DEBUG'     => Monolog\Logger::DEBUG,
        'INFO'      => Monolog\Logger::INFO,
        'NOTICE'    => Monolog\Logger::NOTICE,
        'WARNING'   => Monolog\Logger::WARNING,
        'ERROR'     => Monolog\Logger::ERROR,
        'CRITICAL'  => Monolog\Logger::CRITICAL,
        'ALERT'     => Monolog\Logger::ALERT,
        'EMERGENCY' => Monolog\Logger::EMERGENCY,
    );

    /**
     * saves already created loggers
     *
     * @var array
     */
    private $logger = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Creates a single Monolog\Logger object depend on assigned logger name
     * and configuration. Created loggers are cached for multiusage.
     *
     * @param string $name
     *
     * @return Monolog\Logger
     *
     * @throws MonologCreator\Exception
     */
    public function createLogger($name)
    {
        // short circuit for cached logger objects
        if (true === array_key_exists($name, $this->logger)) {
            return $this->logger[$name];
        }

        $loggerConfig  = $this->getLoggerConfig($name);
        $handlers      = $this->createHandlers($loggerConfig);
        $processors    = $this->createProcessors($loggerConfig);
        $logger        = new Monolog\Logger(
            $name,
            $handlers,
            $processors
        );

        // cache created logger
        $this->logger[$name] = $logger;

        return $logger;
    }

    /**
     * @param array $loggerConfig
     *
     * @return array
     *
     * @throws MonologCreator\Exception
     */
    public function createHandlers(array $loggerConfig)
    {
        $handlers         = array();
        $formatterFactory = new MonologCreator\Factory\Formatter(
            $this->config
        );
        $handlerFactory   = new MonologCreator\Factory\Handler(
            $this->config,
            $this->levels,
            $formatterFactory
        );

        foreach ($loggerConfig['handler'] as $handlerType) {
            $handlers[] = $handlerFactory->create(
                $handlerType,
                $loggerConfig['level']
            );
        }

        return $handlers;
    }

    /**
     * @param array $loggerConfig
     *
     * @return array
     *
     * @throws MonologCreator\Exception
     */
    public function createProcessors(array $loggerConfig)
    {
        $processors = array();

        if (
            false === array_key_exists('processors', $loggerConfig)
            || false === is_array($loggerConfig['processors'])
        ) {
            return $processors;
        }

        foreach ($loggerConfig['processors'] as $processor) {
            if ('web' === $processor) {
                $webProcessor = new Monolog\Processor\WebProcessor();
                $webProcessor->addExtraField('user_agent', 'HTTP_USER_AGENT');
                $webProcessor->addExtraField('client_ip', 'HTTP_X_CLIENT_IP');

                $processors[] = $webProcessor;
            } elseif ('requestId' === $processor) {
                $processors[] = new Processor\RequestId();
            } elseif ('extraField' === $processor) {
                $extraFields = [];

                if (
                    true === array_key_exists('extraFields', $loggerConfig)
                    && true === \is_array($loggerConfig['extraFields'])
                ) {
                    $extraFields = $loggerConfig['extraFields'];
                }

                $processors[] = new Processor\ExtraFieldProcessor($extraFields);
            } else {
                throw new MonologCreator\Exception(
                    'processor type: ' . $processor . ' is not supported'
                );
            }
        }

        return $processors;
    }

    /**
     * @param string $name
     *
     * @return array
     *
     * @throws MonologCreator\Exception
     */
    private function getLoggerConfig($name)
    {
        if (false === array_key_exists('logger', $this->config)) {
            throw new MonologCreator\Exception("no logger configuration found");
        }

        if (false === array_key_exists('_default', $this->config['logger'])) {
            throw new MonologCreator\Exception(
                "no configuration found for logger: _default"
            );
        }

        $loggerConfig = $this->config['logger']['_default'];

        if (true === array_key_exists($name, $this->config['logger'])) {
            $loggerConfig  = $this->config['logger'][$name];
        }

        if (false === array_key_exists('handler', $loggerConfig)) {
            throw new MonologCreator\Exception(
                "no handler configurated for logger: " . $name
            );
        }

        if (false === array_key_exists('level', $loggerConfig)) {
            throw new MonologCreator\Exception(
                "no level configurated for logger: " . $name
            );
        }

        if (false === array_key_exists($loggerConfig['level'], $this->levels)) {
            throw new MonologCreator\Exception(
                "invalid level: " . $loggerConfig['level']
            );
        }

        return $loggerConfig;
    }
}
