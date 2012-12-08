<?php
/**
 * File containing the abstract ezcDocumentAlnumListItemGenerator base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * List item generator
 *
 * Abstract base class for alphanumeric list item generators, which implements 
 * an applyStyle() method and an additional constructor argument, so that all 
 * alphanumeric list item generators extending from this class cann be called 
 * to generate lower- and uppercase variants of their list items.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentAlnumListItemGenerator extends ezcDocumentListItemGenerator
{
    /**
     * Constant forcing uppercase alphanumeric list items
     */
    const UPPER = 1;

    /**
     * Constant forcing lowercase alphanumeric list items
     */
    const LOWER = 2;

    /**
     * Style defining if the alphanumeric list items should be
     * lower or upper case.
     * 
     * @var int
     */
    protected $style;

    /**
     * Constructn for upper/lower output
     * 
     * @param int $style 
     * @return void
     */
    public function __construct( $style = self::LOWER )
    {
        $this->style = $style === self::LOWER ? self::LOWER : self::UPPER;
    }

    /**
     * Apply upper/lower-case style to return value.
     * 
     * @param string $string 
     * @return string
     */
    protected function applyStyle( $string )
    {
        switch ( $this->style )
        {
            case self::LOWER:
                return strtolower( $string );

            case self::UPPER:
                return strtoupper( $string );
        }
    }
}

?>
