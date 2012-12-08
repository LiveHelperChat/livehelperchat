<?php
/**
 * File containing the ezcDocumentPcssStyleMeasureBoxValue class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive measure box value representation
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleMeasureBoxValue extends ezcDocumentPcssStyleBoxValue
{
    /**
     * Get sub value handler
     * 
     * @return ezcDocumentPcssStyleValue
     */
    protected function getSubValue()
    {
        return 'ezcDocumentPcssStyleMeasureValue';
    }
}

?>
