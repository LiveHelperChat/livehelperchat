<?php
/**
 * File containing the ezcDocumentOdtElementFootnoteFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <text:note/> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementFootnoteFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $element->setProperty( 'type', 'footnote' );
        $citations = $element->getElementsByTagNameNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'note-citation'
        );

        // Should be only 1, foreach to remove all
        foreach ( $citations as $cite )
        {
            $attrs = $element->getProperty( 'attributes' );
            if ( $attrs === false )
            {
                $attrs = array();
            }
            $attrs['label'] = $cite->nodeValue;
            $element->setProperty( 'attributes', $attrs );
            $element->removeChild( $cite );
        }
    }

    /**
     * Check if filter handles the current element.
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function handles( DOMElement $element )
    {
        return ( $element->namespaceURI === ezcDocumentOdt::NS_ODT_TEXT
            && $element->localName === 'note' );
    }
}

?>
