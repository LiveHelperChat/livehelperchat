<?php
/**
 * File containing the ezcDocumentOdtElementFrameFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <draw:frame/> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementFrameFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $parent = $element->parentNode;
        if ( $parent->namespaceURI === ezcDocumentOdt::NS_ODT_TEXT && $parent->localName === 'p' )
        {
            $element->setProperty( 'type', 'inlinemediaobject' );
        }
        else
        {
            $element->setProperty( 'type', 'mediaobject' );
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
        return ( $element->namespaceURI === ezcDocumentOdt::NS_ODT_DRAWING
            && $element->localName === 'frame' );
    }
}

?>
