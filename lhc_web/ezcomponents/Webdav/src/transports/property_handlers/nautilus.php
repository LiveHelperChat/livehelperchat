<?php
/**
 * File containing the ezcWebdavNautilusPropertyHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Property handler adjusted for the GNOME Nautilus client.
 *
 * This property handler removes the "charset=..." part form getcontentype
 * properties, since Nautilus displays them not nicely.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavNautilusPropertyHandler extends ezcWebdavPropertyHandler
{
    /**
     * Returns the XML representation of a live property.
     *
     * Returns a DOMElement, representing the content of the given $property.
     * The newly created element is also appended as a child to the given
     * $parentElement.
     *
     * This method only takes care for {@link ezcWebdavGetContentTypeProperty}
     * and does not add the "charset=..." part to the generated XML output,
     * since Nautilus does not display this nicely. All other properties are
     * dispatched to the default {@link ezcWebdavPropertyHandler}.
     * 
     * @param ezcWebdavLiveProperty $property 
     * @param DOMElement $parentElement 
     * @return DOMElement
     */
    protected function serializeLiveProperty( ezcWebdavLiveProperty $property, DOMElement $parentElement )
    {
        switch ( get_class( $property ) )
        {
            case 'ezcWebdavGetContentTypeProperty':
                $elementName  = 'getcontenttype';
                $elementValue = ( $property->mime !== null ? $property->mime : null );
                break;
            default:
                return parent::serializeLiveProperty( $property, $parentElement );
        }

        $propertyElement = $parentElement->appendChild( 
            ezcWebdavServer::getInstance()->xmlTool->createDomElement( $parentElement->ownerDocument, $elementName, $property->namespace )
        );

        if ( $elementValue instanceof DOMDocument )
        {
            $propertyElement->appendChild(
                $dom->importNode( $elementValue->documentElement, true )
            );
        }
        else if ( is_array( $elementValue ) )
        {
            foreach ( $elementValue as $subValue )
            {
                $propertyElement->appendChild( $subValue );
            }
        }
        else if ( is_scalar( $elementValue ) )
        {
            $propertyElement->nodeValue = $elementValue;
        }

        return $propertyElement;
    }
}

?>
