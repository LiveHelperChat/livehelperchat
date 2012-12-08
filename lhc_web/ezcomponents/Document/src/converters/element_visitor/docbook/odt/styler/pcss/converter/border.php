<?php
/**
 * File containing the ezcDocumentOdtPcssBorderConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style converter for border style properties.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtPcssBorderConverter implements ezcDocumentOdtPcssConverter
{
    /**
     * Converts CSS 'border' style.
     *
     * This method receives a $targetProperty DOMElement and converts the given 
     * style with $styleName and $styleValue to attributes on this 
     * $targetProperty.
     * 
     * @param DOMElement $targetProperty 
     * @param string $styleName 
     * @param ezcDocumentPcssStyleValue $styleValue 
     */
    public function convert( DOMElement $targetProperty, $styleName, ezcDocumentPcssStyleValue $styleValue )
    {
        foreach ( $styleValue->value as $type => $borderValues )
        {
            $targetProperty->setAttributeNS(
                ezcDocumentOdt::NS_ODT_FO,
                "fo:border-{$type}",
                sprintf(
                    "%smm %s %s",
                    $borderValues['width'],
                    $borderValues['style'],
                    ezcDocumentOdtPcssConverterTools::serializeColor( $borderValues['color'] )
                )
            );
        }
    }
}

?>
