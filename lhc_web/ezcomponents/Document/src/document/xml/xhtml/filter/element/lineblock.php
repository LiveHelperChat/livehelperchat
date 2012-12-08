<?php
/**
 * File containing the ezcDocumentXhtmlLineBlockElementFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHtml line blocks
 *
 * There is no semantic markup for something like line blocks in HTML. Line
 * blocks are basically text with manual breaks at the end of each line (like
 * in poems). In HTML this is often indicated by a paragraph with several br
 * tags inside.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlLineBlockElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        if ( $element->tagName === 'p' )
        {
            $element->setProperty( 'type', 'literallayout' );
            $element->setProperty( 'attributes', array(
                'class' => 'normal',
            ) );
        }
        else
        {
            $element->appendChild( new DOMText( "\n" ) );
            $element->setProperty( 'whitespace', 'significant' );
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
        return ( $element->tagName === 'br' ) ||
               ( ( $element->tagName === 'p' ) &&
                 ( ( $this->hasClass( $element, 'lineblock' ) ||
                   ( $element->getElementsByTagName( 'br' )->length ) ) ) );
    }
}

?>
