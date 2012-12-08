<?php
/**
 * File containing the ezcDocumentOdtElementLinkFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <text:a/> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementLinkFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $element->setProperty( 'type', 'ulink' );
        $attrs = $element->getProperty( 'attributes' );
        if ( !is_array( $attrs ) )
        {
            $attrs = array();
        }
        // @todo: Can we convert more attributes here? Maybe <ulink type="…"/>?
        $attrs['url'] = $element->getAttributeNS(
            ezcDocumentOdt::NS_XLINK,
            'href'
        );
        $element->setProperty( 'attributes', $attrs );
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
            && $element->localName === 'a' );
    }
}

?>
