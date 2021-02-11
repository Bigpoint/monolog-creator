<?php


namespace MonologCreator\Processor;

/**
 * Class ExtraFieldProcessor
 *
 * Allows adding additional high-level or special fields to the log output.
 *
 * @package MonologCreator\Processor
 * @author Sebastian Götze <s.goetze@bigpoint.net>
 */
class ExtraFieldProcessor implements \Monolog\Processor\ProcessorInterface
{
    /**
     * Array to hold additional fields
     *
     * @var array
     */
    protected $_extraFields = array();

    /**
     * ExtraFieldProcessor constructor.
     *
     * @param array|null $extraFields
     */
    public function __construct(array $extraFields = array())
    {
        $this->_extraFields = $extraFields;
    }

    /**
     * Invoke processor
     *
     * Adds fields to record before returning it.
     *
     * @param array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        if (!is_array($record['extra'])) {
            $record['extra'] = array();
        }

        // Add fields to record
        $record['extra'] = array_merge($record['extra'], $this->_extraFields);

        return $record;
    }
}