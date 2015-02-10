<?php
namespace MonologCreator\Factory;

use \MonologCreator;
use \Monolog;

/**
 * Class Handler
 *
 * @package MonologCreator\Factory
 */
class Handler
{
    /**
     * @var array
     */
    private $_config = array();

    /**
     * @var array
     */
    private $_levels = array();

    /**
     * @var MonologCreator\Factory\Formatter
     */
    private $_formatterFactory = null;

    /**
     * @param array     $config
     * @param array     $levels
     * @param Formatter $formatterFactory
     */
    public function __construct(
        array $config,
        array $levels,
        MonologCreator\Factory\Formatter $formatterFactory
    ) {
        $this->_config           = $config;
        $this->_levels           = $levels;
        $this->_formatterFactory = $formatterFactory;
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
    public function create($handlerType, $level)
    {
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

        $handler       = null;
        $handlerConfig = $this->_config['handler'][$handlerType];

        // evaluate handler
        if ('stream' === $handlerType) {
            $handler = $this->_createStreamhandler($handlerConfig, $level);
        } elseif ('udp' === $handlerType) {
            $handler = $this->_createUdphandler($handlerConfig, $level);
        } else {
            throw new MonologCreator\Exception(
                'handler type: ' . $handlerType . ' is not supported'
            );
        }

        // set formatter
        if (true === array_key_exists('formatter', $handlerConfig)) {
            $handler->setFormatter(
                $this->_formatterFactory->create($handlerConfig['formatter'])
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
}
