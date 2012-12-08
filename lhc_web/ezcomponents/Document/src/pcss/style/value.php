<?php
/**
 * File containing the abstract ezcDocumentPcssStyleValue base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive value representation.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentPcssStyleValue extends ezcBaseStruct
{
    /**
     * Directive value
     *
     * @var mixed
     */
    public $value;

    /**
     * Construct value
     *
     * Optionally pass a parsed representation of the value.
     * 
     * @param mixed $value 
     * @return void
     */
    public function __construct( $value = null )
    {
        $this->value = $value;
    }

    /**
     * Parse value string representation
     *
     * Parse the string representation of the value into a usable
     * representation.
     * 
     * @param string $value 
     * @return ezcDocumentPcssStyleValue
     */
    abstract public function parse( $value );

    /**
     * Get regular expression matching the value
     *
     * Return a regular sub expression, which matches all possible values of
     * this value type. The regular expression should NOT contain any named
     * sub-patterns, since it might be repeatedly embedded in some box parser.
     * 
     * @return string
     */
    abstract public function getRegularExpression();

    /**
     * Convert value to string
     *
     * @return string
     */
    abstract public function __toString();
}
?>
