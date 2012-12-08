<?php
/**
 * File containing the ezcDocumentPcssStyleListValue class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive list value representation
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleListValue extends ezcDocumentPcssStyleValue
{
    /**
     * Parse value string representation
     *
     * Parse the string representation of the value into a usable
     * representation.
     * 
     * @param string $value 
     * @return void
     */
    public function parse( $value )
    {
        $this->value = preg_split( '(\s+)', $value, -1, PREG_SPLIT_NO_EMPTY );
        return $this;
    }

    /**
     * Get regular expression matching the value
     *
     * Return a regular sub expression, which matches all possible values of
     * this value type. The regular expression should NOT contain any named
     * sub-patterns, since it might be repeatedly embedded in some box parser.
     * 
     * @return string
     */
    public function getRegularExpression()
    {
        return '(\S+\s*)+';
    }

    /**
     * Convert value to string
     *
     * @return string
     */
    public function __toString()
    {
        return implode( ' ', $this->value );
    }
}
?>
