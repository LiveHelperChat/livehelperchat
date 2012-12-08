<?php
/**
 * File containing the ezcDocumentOdtElementImageFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <draw:image> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementImageFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $frame = $element->parentNode;

        $element->setProperty( 'type', 'imageobject' );

        $imageData = new ezcDocumentPropertyContainerDomElement(
            'imagedata',
            null,
            ezcDocumentOdt::NS_EZC
        );
        $this->insertImageData( $element, $imageData );
        $imageData->setProperty( 'type', 'imagedata' );
        
        $attributes = array(
            'fileref' => $element->getAttributeNS(
                ezcDocumentOdt::NS_XLINK,
                'href'
            )
        );
        if ( $frame->hasAttributeNS( ezcDocumentOdt::NS_ODT_SVG, 'width' ) )
        {
            $attributes['width'] = $frame->getAttributeNS( ezcDocumentOdt::NS_ODT_SVG, 'width' );
        }
        if ( $frame->hasAttributeNS( ezcDocumentOdt::NS_ODT_SVG, 'height' ) )
        {
            $attributes['depth'] = $frame->getAttributeNS( ezcDocumentOdt::NS_ODT_SVG, 'height' );
        }

        $imageData->setProperty(
            'attributes',
            $attributes
        );
    }

    /**
     * Inserts $imageData as a child into $imageObject.
     *
     * Detects if $imageObject contains <office:binary-data/>. If this is the case, 
     * this element is replaced with the given $imageData. Otherwise, 
     * $imageData is added as a new child.
     * 
     * @param DOMElement $imageObject 
     * @param DOMElement $imageData 
     */
    protected function insertImageData( $imageObject, $imageData )
    {
        $binaryDataElems = $imageObject->getElementsByTagNameNS(
            ezcDocumentOdt::NS_ODT_OFFICE,
            'binary-data'
        );
        if ( $binaryDataElems->length === 1 )
        {
            $imageObject->replaceChild( $imageData, $binaryDataElems->item( 0 ) );
        }
        else
        {
            $imageObject->appendChild( $imageData );
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
        return ( $element->namespaceURI === ezcDocumentOdt::NS_ODT_DRAWING
            && $element->localName === 'image' );
    }
}

?>
