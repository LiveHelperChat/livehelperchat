<?php
/**
 * File containing the ezcDocumentOdtElementHeaderFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <text:h/> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementHeaderFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $currentLevel = $element->getAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'outline-level'
        );

        $parent = $element->parentNode;
        $siblings = $parent->childNodes;

        $section = new ezcDocumentPropertyContainerDomElement(
            'section',
            null,
            ezcDocumentOdt::NS_EZC
        );
        $parent->replaceChild( $section, $element );
        $section->setProperty( 'type', 'section' );
        $section->setProperty( 'level', $currentLevel );

        $section->appendChild( $element );
        $element->setProperty( 'type', 'title' );

        for ( $i = 0; $i < $siblings->length; ++$i )
        {
            if ( $siblings->item( $i )->isSameNode( $section ) )
            {
                break;
            }
        }
        ++$i;

        while ( ( $sibling = $siblings->item( $i ) ) !== null )
        {
            if ( $sibling->nodeType === XML_ELEMENT_NODE
                 && $sibling->namespaceURI === ezcDocumentOdt::NS_EZC
                 && $sibling->getProperty( 'level' ) <= $currentLevel
               ) 
            {
                // Reached next higher or same level section
                break;
            }

            $section->appendChild( $sibling->cloneNode( true ) );
            $parent->removeChild( $sibling );
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
            && $element->localName === 'h' );
    }
}

?>
