<?php
/**
 * File containing the ezcDocumentPdfTableColumnWidthCalculator class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Table column width calculator
 *
 * Base class for a table column width calculator, which is responsible to 
 * estimate / guess / calculate sensible column width for a docbook table
 * definitions.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentPdfTableColumnWidthCalculator
{
    /**
     * Estimate column widths
     *
     * Should return an array with the column widths given as float numbers 
     * between 0 and 1, which all add together to 1.
     * 
     * @param DOMElement $table 
     * @return array
     */
    abstract public function estimateWidths( DOMElement $table );
}
?>
