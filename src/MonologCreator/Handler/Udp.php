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
    /**
     * @var \Monolog\Handler\SyslogUdp\UdpSocket
     */
    private $_socket = null;

    /**
     * @param \Monolog\Handler\SyslogUdp\UdpSocket $socket
     * @param bool|int                             $level
     * @param bool                                 $bubble
     */
    public function __construct(
        \Monolog\Handler\SyslogUdp\UdpSocket $socket,
        $level = \Monolog\Logger::DEBUG,
        $bubble = true
    ) {
        parent::__construct($level, $bubble);

        $this->_socket = $socket;
    }

    /**
     * @param array $record
     */
    protected function write(array $record)
    {
        $lines = $this->splitMessageIntoLines($record['formatted']);

        foreach ($lines as $line) {
            $this->_socket->write($line);
        }
    }

    /**
     * @return null
     */
    public function close()
    {
        $this->_socket->close();
    }

    /**
     * @param $message
     *
     * @return array
     */
    private function splitMessageIntoLines($message)
    {
        if (is_array($message)) {
            $message = implode("\n", $message);
        }

        return preg_split('/$\R?^/m', $message);
    }

    /**
     * Inject your own socket, mainly used for testing.
     *
     * @param \Monolog\Handler\SyslogUdp\UdpSocket $socket
     */
    public function setSocket($socket)
    {
        $this->_socket = $socket;
    }
}
