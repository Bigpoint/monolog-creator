<?php
namespace MonologCreator;

use \MonologCreator;
use \Monolog;

/**
 * Factory class to for creating monolog loggers with preconfigurated array
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
    private $_logger = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
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
        // short circut for cached logger objects
        if (true === array_key_exists($name, $this->_logger)) {
            return $this->_logger[$name];
        }

        $loggerConfig  = $this->_getLoggerConfig($name);
        $handlers      = $this->createHandlers($loggerConfig);
        $processors    = $this->createProcessors($loggerConfig);
        $logger        = new Monolog\Logger(
            $name,
            $handlers,
            $processors
        );

        // cache created logger
        $this->_logger[$name] = $logger;

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
        $handlers = array();

        foreach ($loggerConfig['handler'] as $handlerType) {
            $handlers[] = $this->_createHandler(
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

        if (false === array_key_exists('processors', $loggerConfig)
            || false === is_array($loggerConfig['processors'])
        ) {
            return $processors;
        }

        foreach ($loggerConfig['processors'] as $processor) {
            if ('web' === $processor) {
                $webProcessor = new Monolog\Processor\WebProcessor();
                $webProcessor->addExtraField('user_agent', 'HTTP_USER_AGENT');

                $processors[] = $webProcessor;
            } else {
                throw new Logger\Exception(
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
    private function _getLoggerConfig($name)
    {
        if (false === array_key_exists('logger', $this->_config)) {
            throw new MonologCreator\Exception("no logger configuration found");
        }

        if (false === array_key_exists('_default', $this->_config['logger'])) {
            throw new MonologCreator\Exception(
                "no configuration found for logger: _default"
            );
        }

        $loggerConfig = $this->_config['logger']['_default'];

        if (true === array_key_exists($name, $this->_config['logger'])) {
            $loggerConfig  = $this->_config['logger'][$name];
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

        if (false === array_key_exists($loggerConfig['level'], $this->_levels)) {
            throw new MonologCreator\Exception(
                "invalid level: " . $loggerConfig['level']
            );
        }

        return $loggerConfig;
    }

    /**
     * creates specific monolog handlers
     *
     * @param string $handlerType
     * @param string $level
     *
     * @return Monolog\Handler\HandlerInterface
     *
     * @throws MonologCreator\Exception
     */
    private function _createHandler(
        $handlerType,
        $level
    ) {

        if (false === array_key_exists('handler', $this->_config)) {
            throw new MonologCreator\Exception(
                'no handler configuration found'
            );
        }

        if (false === array_key_exists($handlerType, $this->_config['handler'])) {
            throw new MonologCreator\Exception(
                'no handler configuration found for handlerType: '
                . $handlerType
            );
        }

        $handler = null;
        $handlerConfig = $this->_config['handler'][$handlerType];

        // evaluate handler
        if ('stream' === $handlerType) {
            $handler = $this->_createStreamhandler($handlerConfig, $level);

        } else if ('udp' === $handlerType) {
            $handler = $this->_createUdphandler($handlerConfig, $level);

        } else {
            throw new MonologCreator\Exception(
                'handler type: ' . $handlerType . ' is not supported'
            );
        }

        // set formatter
        if (true === array_key_exists('formatter', $handlerConfig)) {
            $handler->setFormatter(
                $this->_createFormatter($handlerConfig['formatter'])
            );
        }

        return $handler;
    }

    /**
     * @param  array  $handlerConfig
     * @param  string $level
     *
     * @return Monolog\Handler\StreamHandler
     *
     * @throws MonologCreator\Exception
     */
    private function _createStreamHandler(array $handlerConfig, $level)
    {
        if (false === array_key_exists('path', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'path configuration for stream handler is missing'
            );
        }

        return new Monolog\Handler\StreamHandler(
            $handlerConfig['path'],
            $this->_levels[$level]
        );
    }

    /**
     * @param  array  $handlerConfig
     * @param  string $level
     *
     * @return MonologCreator\Handler\Udp
     *
     * @throws MonologCreator\Exception
     */
    private function _createUdpHandler(array $handlerConfig, $level)
    {
        if (false === array_key_exists('host', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'host configuration for udp handler is missing'
            );
        }

        if (false === array_key_exists('port', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'port configuration for udp handler is missing'
            );
        }

        return new MonologCreator\Handler\Udp(
            $this->_createUdpSocket(
                $handlerConfig['host'],
                $handlerConfig['port']
            ),
            $this->_levels[$level]
        );
    }

    /**
     * @param  string $host
     * @param  int    $port
     *
     * @return Monolog\Handler\SyslogUdp\UdpSocket
     *
     * @codeCoverageIgnore
     */
    protected function _createUdpSocket($host, $port)
    {
        return new Monolog\Handler\SyslogUdp\UdpSocket(
            $host,
            $port
        );
    }

    /**
     * @param  string $formatterType
     *
     * @return Monolog\Formatter\FormatterInterface
     *
     * @throws MonologCreator\Exception
     */
    private function _createFormatter($formatterType)
    {
        if (false === array_key_exists('formatter', $this->_config)) {
            throw new MonologCreator\Exception(
                'no formatter configuration found'
            );
        }

        if (false === array_key_exists($formatterType, $this->_config['formatter'])) {
            throw new MonologCreator\Exception(
                'no formatter configuration found for formatterType: '
                . $formatterType
            );
        }

        $formatterConfig = $this->_config['formatter'][$formatterType];

        if ('logstash' === $formatterType) {
            return $this->_createLogstashFormatter($formatterConfig);
        }

        throw new MonologCreator\Exception(
            'formatter type: ' . $formatterType . ' is not supported'
        );
    }

    /**
     * @param  array $formatterConfig
     *
     * @return Monolog\Formatter\LogstashFormatter
     *
     * @throws MonologCreator\Exception
     */
    private function _createLogstashFormatter(array $formatterConfig)
    {
        if (false === array_key_exists('type', $formatterConfig)) {
            throw new MonologCreator\Exception(
                'type configuration for logstash foramtter is missing'
            );
        }

        return new Monolog\Formatter\LogstashFormatter(
            $formatterConfig['type'],
            null,
            null,
            'ctxt_',
            Monolog\Formatter\LogstashFormatter::V1
        );
    }
}
