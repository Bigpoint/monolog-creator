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
    private $extraFields = array();

    public function __construct(array $extraFields = array())
    {
        $this->extraFields = $extraFields;
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
        if (false === \is_array($record['extra'])) {
            $record['extra'] = array();
        }

        // Add fields to record
        $record['extra'] = \array_merge($record['extra'], $this->extraFields);

        return $record;
    }
}
