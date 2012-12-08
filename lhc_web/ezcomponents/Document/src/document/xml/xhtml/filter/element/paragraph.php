<?php
/**
 * File containing the ezcDocumentXhtmlParagraphElementFilter class
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
class ezcDocumentXhtmlParagraphElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        // Only decorate non-empty paragraphs
        if ( trim( $element->textContent ) &&
             ( !$this->isInline( $element ) ) &&
             ( $element->getProperty( 'type' ) === false ) )
        {
            $element->setProperty( 'type', 'para' );
        }
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
        return ( $element->tagName === 'p' );
    }
}

?>
