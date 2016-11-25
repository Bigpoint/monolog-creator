<?php

namespace MonologCreator\Processor;

/**
 * Injects a per-request UUID into the log output.
 */
class RequestId
{
    /**
     * @var string
     */
    private $_uuid = '';

    /**
     * Called by Monolog - Allows processors to manipulate data.
     *
     *
     * @param  array $record    The record to log
     *
     * @return array            The updated record to log
     */
    public function __invoke(array $record)
    {
        if (true === empty($this->_uuid)) {
            $this->_uuid = $this->_generateUUID();
        }
        $record['extra']['request_id'] = $this->_uuid;

        return $record;
    }

    /**
     * Generate a valid UUIDv4 utilizing the system´s available (P)RNGs.
     *
     * @return string valid UUIDv4
     */
    protected function _generateUUID()
    {
        $data = null;
        switch (true) {
            case $this->_isCallable('random_bytes'):
                $data = $this->_randomBytes(16);
                break;
            case $this->_isCallable('openssl_random_pseudo_bytes'):
                $data = $this->_opensslRandomPseudoBytes(16);
                break;
            case $this->_isCallable('mt_rand'):
                $data = $this->_generateBytesWithMtRand(16);
                break;
            default:
                return 'unavailable';
        }

        return $this->_generateUUIDFromData($data);
    }

    /**
     * Proxy around random_bytes
     *
     * @param $amt int
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected function _randomBytes($amt)
    {
        return random_bytes($amt);
    }

    /**
     * Proxy around openssl_random_pseudo_bytes
     *
     * @param $amt int
     *
     * @return string
     * @codeCoverageIgnore
     */
    protected function _opensslRandomPseudoBytes($amt)
    {
        return openssl_random_pseudo_bytes($amt);
    }

    /**
     * Generate n Bytes with mt_rand
     *
     * @param $amt int
     *
     * @return string
     */
    protected function _generateBytesWithMtRand($amt)
    {
        $tmp = array();

        for ($idx = 0; $idx < $amt; $idx++) {
            $tmp[] = chr($this->_mtRand(0, 255));
        }

        return join('', $tmp);
    }

    /**
     * Proxy around mt_rand
     *
     * @param $min int
     * @param $max int
     *
     * @return int
     * @codeCoverageIgnore
     */
    protected function _mtRand($min, $max)
    {
        return mt_rand($min, $max);
    }

    /**
     * Proxy around is_callabe
     *
     * @param $callable
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isCallable($callable)
    {
        return is_callable($callable);
    }

    /**
     * Generate a valid UUIDv4 from provided random data.
     *
     * A UUIDv4 is formatted
     * xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     * where x is 0-9, A-F and y is 8-9, A-B.
     *
     * @param $data     string  random bytes.
     *
     * @return string           valid, formatted UUIDv4
     */
    private function _generateUUIDFromData($data)
    {
        $data = substr($data, 0, 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set the '4' in block 3
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits for block 4

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
