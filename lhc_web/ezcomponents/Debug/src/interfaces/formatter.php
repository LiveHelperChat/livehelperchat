<?php
/**
 * File containing the ezcDebugOutputFormatter class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDebugOutputFormatter provides the common interface for all classes writing debug output.
 *
 * @package Debug
 * @version 1.2.1
 */
interface ezcDebugOutputFormatter
{
    /**
     * Returns a string containing the formatted output based on $timerData and $writerData.
     *
     * @param array(ezcDebugStructure) $timerData
     * @param array $writerData
     * @return string
     */
    function generateOutput( array $timerData, array $writerData );
}

?>
