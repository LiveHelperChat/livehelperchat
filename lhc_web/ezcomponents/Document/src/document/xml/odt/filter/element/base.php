<?php
/**
 * File containing the abstract ezcDocumentOdtElementBaseFilter base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Basic filter class for ODT element filters.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
abstract class ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    abstract public function filterElement( DOMElement $element );

    /**
     * Check if filter handles the current element
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    abstract public function handles( DOMElement $element );
}

?>
