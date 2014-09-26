<?php

/**
 * File containing the ezcDebugSwitchTimerStruct.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Struct defining a timer switch.
 *
 * @package Debug
 * @version 1.2.1
 * @access private
 */
class ezcDebugSwitchTimerStruct extends ezcBaseStruct
{
    /**
     * The name of the timer took over the old timer.
     *
     * @var string
     */ 
    public $name;   

    /** 
     * The current time.
     *
     * @var float
     */
    public $time;   
}
?>
