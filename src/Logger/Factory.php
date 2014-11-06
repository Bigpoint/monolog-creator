<?php

namespace Logger;
use Logger;

/**
 * Factory class to for creating monolog loggers via configuration array
 */
class Factory
{
    /**
     * @var array
     */
    private $_config = array();

    /**
     * @var array
     */
    private $_levels = array(
        'DEBUG'     => \Monolog\Logger::DEBUG,
        'INFO'      => \Monolog\Logger::INFO,
        'NOTICE'    => \Monolog\Logger::NOTICE,
        'WARNING'   => \Monolog\Logger::WARNING,
        'ERROR'     => \Monolog\Logger::ERROR,
        'CRITICAL'  => \Monolog\Logger::CRITICAL,
        'ALERT'     => \Monolog\Logger::ALERT,
        'EMERGENCY' => \Monolog\Logger::EMERGENCY,
    );

    /**
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * @return \Monolog\Logger
     *
     * @throws Logger\Exception
     */
    public function createLogger($name)
    {
        $logger = new \Monolog\Logger($name);

        if (false === array_key_exists('logger', $this->_config)) {
            throw new Logger\Exception("no logger configuration found");
        }

        if (false === array_key_exists($name, $this->_config['logger'])) {
            throw new Logger\Exception(
                "no logger configuration found for: " . $name
            );
        }

        $loggerConfig  = $this->_config['logger'][$name];

        if (false === array_key_exists('handler', $loggerConfig)) {
            throw new Logger\Exception(
                "no handler configurated for logger: " . $name
            );
        }

        if (false === array_key_exists('level', $loggerConfig)) {
            throw new Logger\Exception(
                "no level configurated for logger: " . $name
            );
        }

        if (false === array_key_exists($loggerConfig['level'], $this->_levels)) {
            throw new Logger\Exception(
                "invalid level: " . $loggerConfig['level']
            );
        }

        // add handler
        foreach ($loggerConfig['handler'] as $handlerType) {
            $handler = $this->_createHandler(
                $handlerType,
                $loggerConfig['level']
            );
            $logger->pushHandler($handler);
        }

        return $logger;

        // $udpHandler = new Logger\Handler\Udp('192.168.50.48', '9999', \Monolog\Logger::INFO);
        // $udpHandler->pushProcessor(new \Monolog\Processor\WebProcessor());
        // $udpHandler->setFormatter(
        //     new \Monolog\Formatter\LogstashFormatter(
        //         'televisa',
        //         null,
        //         null,
        //         'ctxt_',
        //         \Monolog\Formatter\LogstashFormatter::V1
        //     )
        // );
        // $logger->pushHandler($udpHandler);

        // // register logger as php error handler
        // \Monolog\ErrorHandler::register($logger);
    }

    /**
     * creates specific monolog handlers
     *
     * @param  string $handlerType
     * @param  string $level
     *
     * @return \Monolog\HandlerInterface  $handler
     */
    private function _createHandler(
        $handlerType,
        $level
    ) {

        if (false === array_key_exists('handler', $this->_config)) {
            throw new Exception(
                'no handler configuration found'
            );
        }

        if (false === array_key_exists($handlerType, $this->_config['handler'])) {
            throw new Exception(
                'no handler configuration found for handlerType: '
                . $handlerType
            );
        }

        $handlerConfig = $this->_config['handler'][$handlerType];

        if ('stream' === $handlerType) {

            if (false === array_key_exists('path', $handlerConfig)) {
                throw new Exception(
                    'path configuration for stream handler is missing'
                );
            }

            return new \Monolog\Handler\StreamHandler(
                $handlerConfig['path'],
                $this->_levels[$level]
            );
        }

        throw new Exception(
            'handler type: ' . $handlerType . ' is not supported'
        );
    }
}
