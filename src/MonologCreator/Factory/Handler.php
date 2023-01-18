<?php

namespace MonologCreator\Factory;

use MonologCreator;
use Monolog;

/**
 * @package MonologCreator\Factory
 */
class Handler
{
    public function __construct(
        private array $config,
        private MonologCreator\Factory\Formatter $formatterFactory,
        private \Predis\Client|null $predisClient = null
    ) {
    }

    /**
     * @throws MonologCreator\Exception
     */
    public function create(string $handlerType, string $level): Monolog\Handler\HandlerInterface
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
     * @throws MonologCreator\Exception
     */
    private function createStreamHandler(array $handlerConfig, string $level): Monolog\Handler\StreamHandler
    {
        if (false === array_key_exists('path', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'path configuration for stream handler is missing'
            );
        }

        return new Monolog\Handler\StreamHandler(
            $handlerConfig['path'],
            \Monolog\Level::fromName($level)
        );
    }

    /**
     * @throws MonologCreator\Exception
     */
    private function createUdpHandler(array $handlerConfig, string $level): MonologCreator\Handler\Udp
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
            \Monolog\Level::fromName($level)
        );
    }

    /**
     * @return Monolog\Handler\SyslogUdp\UdpSocket
     *
     * @codeCoverageIgnore
     */
    protected function createUdpSocket(string $host, int $port): Monolog\Handler\SyslogUdp\UdpSocket
    {
        return new Monolog\Handler\SyslogUdp\UdpSocket(
            $host,
            $port
        );
    }

    /**
     * @throws MonologCreator\Exception
     */
    private function createRedisHandler(array $handlerConfig, string $level): Monolog\Handler\RedisHandler
    {
        if (false === array_key_exists('key', $handlerConfig)) {
            throw new MonologCreator\Exception(
                'key configuration for redis handler is missing'
            );
        }

        if ($this->predisClient === null) {
            throw new MonologCreator\Exception(
                'predis client object is not set'
            );
        }

        return new Monolog\Handler\RedisHandler(
            $this->predisClient,
            $handlerConfig['key'],
            \Monolog\Level::fromName($level)
        );
    }
}
