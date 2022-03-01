<?php

namespace MonologCreator\Factory;

use MonologCreator;
use Monolog;

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
    private $config = array();

    /**
     * @var array
     */
    private $levels = array();

    /**
     * @var MonologCreator\Factory\Formatter
     */
    private $formatterFactory = null;

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
        $this->config           = $config;
        $this->levels           = $levels;
        $this->formatterFactory = $formatterFactory;
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
        if (false === array_key_exists('handler', $this->config)) {
            throw new MonologCreator\Exception(
                'no handler configuration found'
            );
        }

        if (false === array_key_exists($handlerType, $this->config['handler'])) {
            throw new MonologCreator\Exception(
                'no handler configuration found for handlerType: '
                . $handlerType
            );
        }

        $handler       = null;
        $handlerConfig = $this->config['handler'][$handlerType];

        // evaluate handler
        if ('stream' === $handlerType) {
            $handler = $this->createStreamHandler($handlerConfig, $level);
        } elseif ('udp' === $handlerType) {
            $handler = $this->createUdpHandler($handlerConfig, $level);
        } elseif ('redis' === $handlerType) {
            $handler = $this->createRedisHandler($handlerConfig, $level);
        } else {
            throw new MonologCreator\Exception(
                'handler type: ' . $handlerType . ' is not supported'
            );
        }

        // set formatter
        if (true === array_key_exists('formatter', $handlerConfig)) {
            $handler->setFormatter(
                $this->formatterFactory->create($handlerConfig['formatter'])
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
    private function createStreamHandler(array $handlerConfig, $level)
    {
        if (false === array_key_exists('path', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'path configuration for stream handler is missing'
            );
        }

        return new Monolog\Handler\StreamHandler(
            $handlerConfig['path'],
            $this->levels[$level]
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
    private function createUdpHandler(array $handlerConfig, $level)
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
            $this->createUdpSocket(
                $handlerConfig['host'],
                $handlerConfig['port']
            ),
            $this->levels[$level]
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
    protected function createUdpSocket($host, $port)
    {
        return new Monolog\Handler\SyslogUdp\UdpSocket(
            $host,
            $port
        );
    }

    /**
     * @param array  $handlerConfig
     * @param string $level
     *
     * @return Monolog\Handler\RedisHandler
     *
     * @throws MonologCreator\Exception
     */
    private function createRedisHandler(array $handlerConfig, $level)
    {
        if (false === array_key_exists('url', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'url configuration for redis handler is missing'
            );
        }

        if (false === array_key_exists('key', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'key configuration for redis handler is missing'
            );
        }

        return new Monolog\Handler\RedisHandler(
            $this->createPredisClient($handlerConfig['url']),
            $handlerConfig['key'],
            $this->levels[$level]
        );
    }

    /**
     * @param string $url
     *
     * @return \Predis\Client
     *
     * @codeCoverageIgnore
     */
    protected function createPredisClient($url)
    {
        return new \Predis\Client($url);
    }
}
