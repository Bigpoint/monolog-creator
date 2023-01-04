<?php

namespace MonologCreator\Handler;

/**
 * Custom Monolog Handler to sent logs via UDP. Its based on
 * \Monolog\Handler\SyslogUdp\UdpSocket.
 *
 * @package Logger\Handler
 *
 * @@codeCoverageIgnore
 */
class Udp extends \Monolog\Handler\AbstractProcessingHandler
{
    public function __construct(
        private \Monolog\Handler\SyslogUdp\UdpSocket $socket,
        int|string|\Monolog\Level $level = \Monolog\Level::DEBUG,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    protected function write(\Monolog\LogRecord $record): void
    {
        $lines = $this->splitMessageIntoLines($record->formatted);

        foreach ($lines as $line) {
            $this->socket->write($line);
        }
    }

    public function close(): void
    {
        $this->socket->close();
    }

    private function splitMessageIntoLines(mixed $message): array
    {
        if (is_array($message)) {
            $message = implode("\n", $message);
        }

        return preg_split('/$\R?^/m', $message);
    }

    public function setSocket(\Monolog\Handler\SyslogUdp\UdpSocket $socket): void
    {
        $this->socket = $socket;
    }
}
