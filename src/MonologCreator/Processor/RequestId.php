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
    private $uuid = '';

    /**
     * Called by Monolog - Allows processors to manipulate data.
     */
    public function __invoke(\Monolog\LogRecord $record): \Monolog\LogRecord
    {
        if (true === empty($this->uuid)) {
            $this->uuid = $this->generateUUID();
        }
        $record->extra['request_id'] = $this->uuid;

        return $record;
    }

    /**
     * Generate a valid UUIDv4 utilizing the system´s available (P)RNGs.
     */
    protected function generateUUID(): string
    {
        $data = null;
        switch (true) {
            case $this->isCallable('random_bytes'):
                $data = $this->randomBytes(16);
                break;
            case $this->isCallable('openssl_random_pseudo_bytes'):
                $data = $this->opensslRandomPseudoBytes(16);
                break;
            case $this->isCallable('mt_rand'):
                $data = $this->generateBytesWithMtRand(16);
                break;
            default:
                return 'unavailable';
        }

        return $this->generateUUIDFromData($data);
    }

    /**
     * Proxy around random_bytes
     * @codeCoverageIgnore
     */
    protected function randomBytes(int $amt): string
    {
        return random_bytes($amt);
    }

    /**
     * Proxy around openssl_random_pseudo_bytes
     * @codeCoverageIgnore
     */
    protected function opensslRandomPseudoBytes(int $amt): string
    {
        return openssl_random_pseudo_bytes($amt);
    }

    /**
     * Generate n Bytes with mt_rand
     */
    protected function generateBytesWithMtRand(int $amt): string
    {
        $tmp = array();

        for ($idx = 0; $idx < $amt; $idx++) {
            $tmp[] = chr($this->mtRand(0, 255));
        }

        return join('', $tmp);
    }

    /**
     * Proxy around mt_rand
     * @codeCoverageIgnore
     */
    protected function mtRand(int $min, int $max): int
    {
        return mt_rand($min, $max);
    }

    /**
     * Proxy around is_callable
     *
     * @codeCoverageIgnore
     */
    protected function isCallable($callable): bool
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
     */
    private function generateUUIDFromData(string $data): string
    {
        $data = substr($data, 0, 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set the '4' in block 3
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits for block 4

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
