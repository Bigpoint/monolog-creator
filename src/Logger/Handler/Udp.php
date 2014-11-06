<?php
namespace Logger\Handler;

class Udp extends \Monolog\Handler\AbstractProcessingHandler
{

    private $_socket = null;

    /**
     * @param string  $host
     * @param int     $port
     * @param mixed   $facility
     * @param integer $level    The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($host, $port, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->_socket = new \Monolog\Handler\SyslogUdp\UdpSocket($host, $port);
    }

    protected function write(array $record)
    {
        $lines = $this->splitMessageIntoLines($record['formatted']);

        foreach ($lines as $line) {
            $this->_socket->write($line);
        }
    }

    public function close()
    {
        $this->_socket->close();
    }

    private function splitMessageIntoLines($message)
    {
        if (is_array($message)) {
            $message = implode("\n", $message);
        }

        return preg_split('/$\R?^/m', $message);
    }

    /**
     * Inject your own socket, mainly used for testing
     */
    public function setSocket($socket)
    {
        $this->_socket = $socket;
    }
}
