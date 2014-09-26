<?php

/**
 * File containing the ezcDebugTimerStruct.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcDebugTimerStruct structure keeps track of the timing data.
 *
 * @package Debug
 * @version 1.2.1
 * @access private
 */
class ezcDebugTimerStruct extends ezcBaseStruct
{
    /**
     * The name of the timer.
     *
     * The name has two purposes:
     * - The (unique) identifier of the running timer.
     * - The description of the timer in the timer summary. 
     * 
     * @var string
     */
    public $name;   

    /** 
     * The source of the timer.
     *
     * @var string
     */
    public $source;   

    /** 
     * The group of the timer.
     *
     * @var string
     */
    public $group;   

    /**
     * An array that contains the switchTimer structures.
     *
     * @var array(ezcDebugSwitchTimerStruct)
     */
    public $switchTime;   

    /**
     * The start time in miliseconds.
     * 
     * @var float
     */
    public $startTime;   

    /**
     * The stop time in miliseconds.
     * 
     * @var float
     */
    public $stopTime;   

    /**
     * The time that elapsed between the startTimer and the stopTimer.
     *
     * @var float
     */
    public $elapsedTime;   

    /**
     * The number of the timer that started.
     * 
     * @var int
     */
    public $startNumber;   
    
    /**
     * The number of the timer that stopped.
     *
     * @var int
     */
    public $stopNumber;

    /**
     * Constructs a new ezcDebugSwitchTimerStruct
     *
     * @param string $name 
     * @param string $source 
     * @param string $group 
     * @param int $switchTime 
     * @param int $startTime 
     * @param int $stopTime 
     * @param int $elapsedTime 
     * @param int $startNumber 
     * @param int $stopNumber 
     */
    public function __construct( $name = null, $source = null, $group = null, $switchTime = null, $startTime = null, $stopTime = null, $elapsedTime = null, $startNumber = null, $stopNumber = null )
    {
        $this->name = $name;
        $this->source = $source;
        $this->group = $group;
        $this->switchTime = $switchTime;
        $this->startTime = $startTime;
        $this->stopTime = $stopTime;
        $this->elapsedTime = $elapsedTime;
        $this->startNumber = $startNumber;
        $this->stopNumber = $stopNumber;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcDebugSwitchTimerStruct
     */
    static public function __set_state( array $array )
    {
        return new ezcDebugTimerStruct(
             $array['name'], $array['source'], $array['group'], $array['switchTime'], $array['startTime'],
             $array['stopTime'], $array['elapsedTime'], $array['startNumber'], $array['stopNumber'] 
        );
    }
}

?>
