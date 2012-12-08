<?php
/**
 * File containing the ezcDocumentXhtmlTableCellElementFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHtml table cells.
 *
 * Tables, where the rows are nor structured into a tbody and thead are
 * restructured into those by this filter.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlTableCellElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        if ( $element->hasAttribute( 'rowspan' ) &&
             ( $element->getAttribute( 'rowspan' ) > 1 ) )
        {
            $attributes = $element->getProperty( 'attributes' );
            $attributes['morerows'] = $element->getAttribute( 'rowspan' ) - 1;
            $element->setProperty( 'attributes', $attributes );
        }

        // @todo: Handle colspan, too - even it is quite complex to express in
        // docbook.
    }

    /**
     * Check if filter handles the current element
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function handles( DOMElement $element )
    {
        return ( $element->tagName === 'td' );
    }
}

?>
