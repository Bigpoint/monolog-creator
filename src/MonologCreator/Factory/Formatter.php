<?php

namespace MonologCreator\Factory;

use MonologCreator;
use Monolog;

/**
 * Class Formatter
 *
 * @package MonologCreator\Factory
 */
class Formatter
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param  string $formatterType
     *
     * @return Monolog\Formatter\FormatterInterface
     *
     * @throws MonologCreator\Exception
     */
    public function create($formatterType)
    {
        if (false === array_key_exists('formatter', $this->config)) {
            throw new MonologCreator\Exception(
                'no formatter configuration found'
            );
        }

        if (false === array_key_exists($formatterType, $this->config['formatter'])) {
            throw new MonologCreator\Exception(
                'no formatter configuration found for formatterType: '
                . $formatterType
            );
        }

        $formatterConfig = $this->config['formatter'][$formatterType];

        if ('logstash' === $formatterType) {
            return $this->createLogstash($formatterConfig);
        }

        if ('line' === $formatterType) {
            return $this->createLine($formatterConfig);
        }

        if ('json' === $formatterType) {
            return $this->createJson($formatterConfig);
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
    private function createLogstash(array $formatterConfig)
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

    /**
     * @param array $formatterConfig
     *
     * @return Monolog\Formatter\LineFormatter
     */
    private function createLine(array $formatterConfig)
    {
        $boolValues = array(
            'true'  => true,
            'false' => false,
        );

        $format = null;
        if (true === array_key_exists('format', $formatterConfig)) {
            $format = $formatterConfig['format'];
        }

        $dateFormat = null;
        if (true === array_key_exists('dateFormat', $formatterConfig)) {
            $dateFormat = $formatterConfig['dateFormat'];
        }

        $includeStacktraces = false;
        if (
            true === array_key_exists('includeStacktraces', $formatterConfig)
            && true === array_key_exists($formatterConfig['includeStacktraces'], $boolValues)
        ) {
            $includeStacktraces = $boolValues[$formatterConfig['includeStacktraces']];
        }

        $allowInlineLineBreaks = false;
        if (
            true === array_key_exists('allowInlineLineBreaks', $formatterConfig)
            && true === array_key_exists($formatterConfig['allowInlineLineBreaks'], $boolValues)
        ) {
            $allowInlineLineBreaks = $boolValues[$formatterConfig['allowInlineLineBreaks']];
        }

        $ignoreEmptyContextAndExtra = false;
        if (
            true === array_key_exists('ignoreEmptyContextAndExtra', $formatterConfig)
            && true === array_key_exists($formatterConfig['ignoreEmptyContextAndExtra'], $boolValues)
        ) {
            $ignoreEmptyContextAndExtra = $boolValues[$formatterConfig['ignoreEmptyContextAndExtra']];
        }

        $formatter = new Monolog\Formatter\LineFormatter($format, $dateFormat);
        $formatter->includeStacktraces($includeStacktraces);
        $formatter->allowInlineLineBreaks($allowInlineLineBreaks);
        $formatter->ignoreEmptyContextAndExtra($ignoreEmptyContextAndExtra);

        return $formatter;
    }

    /**
     * @param array $formatterConfig
     *
     * @return Monolog\Formatter\JsonFormatter
     */
    private function createJson(array $formatterConfig)
    {
        $batchMode     = Monolog\Formatter\JsonFormatter::BATCH_MODE_JSON;
        $appendNewline = true;

        $formatter = new Monolog\Formatter\JsonFormatter(
            $batchMode,
            $appendNewline
        );
        return $formatter;
    }
}
