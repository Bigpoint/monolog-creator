<?php
namespace MonologCreator\Processor;

/**
 * Class Browser
 *
 * @package MonologCreator\Processor
 */
class Browser
{
    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $browser = \get_browser();

        $record['extra']['user_agent'] = $browser->parent;
        $record['extra']['user_os']    = $browser->platform;
var_dump($record); die;
        return $record;
    }
}
