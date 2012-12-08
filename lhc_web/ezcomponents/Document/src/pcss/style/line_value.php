<?php
/**
 * File containing the ezcDocumentPcssStyleLineValue class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive line style value representation
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleLineValue extends ezcDocumentPcssStyleValue
{
    /**
     * Line styles regular expression
     *
     * @var string
     */
    protected $regexp = '(?:none|dotted|dashed|solid|double|groove|ridge|inset|outset|inherit)';

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
        if ( !preg_match( '(^\s*' . $this->regexp . '\s*$)S', $value ) )
        {
            throw new ezcDocumentParserException( E_PARSE, "Invalid line style specification: " . $value );
        }

        $this->value = trim( $value );
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
        return $this->regexp;
    }

    /**
     * Convert value to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}

?>
