<?php

namespace MonologCreator\Factory;

use MonologCreator;
use Monolog;

/**
 * @package MonologCreator\Factory
 */
class Formatter
{
    public function __construct(private array $config)
    {
    }

    /**
     * @throws MonologCreator\Exception
     */
    public function create(string $formatterType): Monolog\Formatter\FormatterInterface
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
     * @throws MonologCreator\Exception
     */
    private function createLogstash(array $formatterConfig): Monolog\Formatter\LogstashFormatter
    {
        if (false === array_key_exists('type', $formatterConfig)) {
            throw new MonologCreator\Exception(
                'type configuration for logstash formatter is missing'
            );
        }

        return new Monolog\Formatter\LogstashFormatter(
            $formatterConfig['type'],
            null,
            'extra',
            'context'
        );
    }

    private function createLine(array $formatterConfig): Monolog\Formatter\LineFormatter
    {
        $boolValues = [
            'true'  => true,
            'false' => false,
        ];

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

    private function createJson(array $formatterConfig): Monolog\Formatter\JsonFormatter
    {
        return new Monolog\Formatter\JsonFormatter();
    }
}
