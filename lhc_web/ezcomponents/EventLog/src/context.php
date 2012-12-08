<?php
/**
 * File containing the ezcLogContext class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Stores the contexts for the severities and sources.
 *
 * @package EventLog
 * @version 1.4
 * @access private
 */
class ezcLogContext
{
    /**
     * Stores the contexts for the severities.
     *
     * @var array(integer=>array)
     */
    protected $severityMap;

    /**
     * Stores the contexts for the sources.
     *
     * @var array(string=>array)
     */
    protected $sourceMap;

    /**
     * Resets this object to its initial state by removing all context mappings.
     */
    public function reset()
    {
        unset( $this->severityMap );
        unset( $this->sourceMap );
    }

    /**
     * Set the context $context for the sevirities specified by $severityMask.
     *
     * If the severity already exist, the value will update the old value with
     * the new one.
     *
     * $context is of the format array('key'=>'value').
     *
     * @param int $severityMask
     *        Bitmask that specifies all the event types that share the given
     *        context.
     * @param array(string=>string) $context
     */
    public function setSeverityContext( $severityMask, $context )
    {
        // For all the matching bits, add the context to the array.
        $input = 1;
        while ( $input <= $severityMask )
        {
            if ( $severityMask & $input )
            {
                if ( !isset( $this->severityMap[$input] ) )
                {
                    $this->severityMap[$input] = array();
                }

                $this->severityMap[$input] = array_merge( (array) $this->severityMap[$input], (array) $context );
            }

            $input <<= 1;
        }
    }

    /**
     * Remove the contexts for the severities given by $severityMask.
     *
     * $severityMask is a bitmask that specifies all the event types that should remove
     * their context.
     *
     * @param int $severityMask
     */
    public function unsetSeverityContext( $severityMask )
    {
        // For all the matching bits, remove the context.
        $input = 1;
        while ( $input <= $severityMask )
        {
            if ( $severityMask & $input )
            {
                unset( $this->severityMap[$input] );
            }

            $input <<= 1;
        }
    }

    /**
     * Set the context $context for each eventSource specified by $eventSources.
     *
     * If a certain key from the given context does already exist, the
     * new value will replace the value stored in the context itself. (Value is
     * updated).
     *
     * @param array(string) $eventSources
     * @param array(string=>string) $context
     *        Specifies the keys and values that should be stored into this
     *        context object.
     */
    public function setSourceContext( array $eventSources, $context )
    {
        foreach ( $eventSources as $eventSource )
        {
            if ( !isset( $this->sourceMap[$eventSource] ) )
            {
                $this->sourceMap[$eventSource] = array();
            }

            $this->sourceMap[$eventSource] = array_merge( (array) $this->sourceMap[$eventSource], (array) $context );
        }
    }

    /**
     * Remove the contexts for the given $eventSources.
     *
     * @param array(string) $eventSources
     */
    public function unsetSourceContext( array $eventSources )
    {
        foreach ( $eventSources as $eventSource )
        {
            unset( $this->sourceMap[$eventSource] );
        }
    }

    /**
     * Returns the complete context for the event type $eventType and event source $eventSource.
     *
     * If there is no context available this method will return an empty array.
     *
     * @param int $eventType   The integer that specifies the event type.
     *                             The range of this integer is 2^(N+):
     *                             ( 1, 2, 4, 8, ... )
     * @param string $eventSource
     * @return array(string=>string)
     */
    public function getContext( $eventType, $eventSource )
    {
        $a = isset( $this->severityMap[$eventType] ) ? $this->severityMap[$eventType] : array();
        $b = isset( $this->sourceMap[$eventSource] ) ? $this->sourceMap[$eventSource] : array();
        return array_merge( $a, $b );
    }
}
?>
