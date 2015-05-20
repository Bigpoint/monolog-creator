<?php
namespace MonologCreator\Processor;

/**
 * Class Browser
 *
 * @package MonologCreator\Processor
 *
 * @@codeCoverageIgnore
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

        return $record;
    }
}
