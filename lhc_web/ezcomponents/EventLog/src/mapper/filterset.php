<?php
/**
 * File containing the ezcLogFilterSet class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mapping of an eventType, eventSource and eventCategory to a mixed variable
 * using a filter set.
 *
 * The ezcLogFilterSet is an implementation of the ezcLogMapper. The
 * ezcLogFilterSet contains a set of ezcLogFilterRules. These rules are
 * processed sequentially. The rule assigned first will be processed first.
 * Each rule determines whether the log message matches with the filter rule. If the
 * log message matches, it calls the writer and decide whether the filter set
 * stops processing.
 *
 * @package EventLog
 * @version 1.4
 */
class ezcLogFilterSet implements ezcLogMapper
{
    /**
     * Rules of the filter set.
     *
     * var array(ezcLogFilterRule)
     */
    private $rules = array();

    /**
     * Hash table that caches the {@link get()} queries to their results.
     *
     * var array(string=>mixed)
     */
    private $cache = array();

    /**
     * Appends a rule to the end of the filter set.
     *
     * @param ezcLogFilter $filter
     */
    public function appendRule( $filter )
    {
        $this->rules[] = $filter;
        $this->clearCache();
    }

    /**
     * Deletes the last rule from the filter set.
     *
     * Returns false if the filter set is empty, otherwise true.
     *
     * @return bool
     */
    public function deleteLastRule()
    {
        if ( sizeof( $this->rules ) > 0 )
        {
            array_pop( $this->rules );
            $this->clearCache();
            return true;
        }

        return false;
    }

    /**
     * Returns the variable assigned to the combination of a severity $severity, source $source,
     * and category $category.
     *
     * @param int $severity
     * @param string $source
     * @param string $category
     * @return mixed
     */
    public function get( $severity, $source, $category )
    {
        // It is likely that the same type of log messages are written after each other.
        if ( isset( $this->cache[ $severity . "_" . $source . "_" . $category ] ) )
        {
            return $this->cache[ $severity . "_" . $source . "_" . $category ];
        }

        // It is not cached, yet.
        $total = array();

        foreach ( $this->rules as $rule )
        {
            if ( $rule->isMatch( $severity, $source, $category ) )
            {
                $total = array_merge( $total, $rule->getContainer() );

                if ( !$rule->shouldContinueProcessing() )
                {
                    break;
                }
            }
        }

        // Add to cache.
        $this->cache[ $severity . "_" . $source . "_" . $category ] = $total;
        return $total;
    }

    /**
     * Clears the cache.
     */
    protected function clearCache()
    {
        $this->cache = array();
    }
}
?>
