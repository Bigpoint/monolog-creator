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
        $browser = get_browser();

        $record['user_agent'] = $browser->parent . ' / ' . $browser->platform;
var_dump($record); die;
        return $record;
    }
}
