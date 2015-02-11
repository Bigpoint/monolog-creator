<?php
namespace MonologCreator\Factory;

use \MonologCreator;
use \Monolog;

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
    private $_config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
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
        if (false === array_key_exists('formatter', $this->_config)) {
            throw new MonologCreator\Exception(
                'no formatter configuration found'
            );
        }

        if (false === array_key_exists($formatterType, $this->_config['formatter'])) {
            throw new MonologCreator\Exception(
                'no formatter configuration found for formatterType: '
                . $formatterType
            );
        }

        $formatterConfig = $this->_config['formatter'][$formatterType];

        if ('logstash' === $formatterType) {
            return $this->_createLogstash($formatterConfig);
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
    private function _createLogstash(array $formatterConfig)
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
}
