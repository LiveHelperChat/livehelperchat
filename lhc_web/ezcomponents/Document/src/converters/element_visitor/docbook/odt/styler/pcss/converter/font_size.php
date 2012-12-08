<?php
/**
 * File containing the ezcDocumentOdtPcssFontSizeConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style converter for font-size style properties.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtPcssFontSizeConverter implements ezcDocumentOdtPcssConverter
{
    /**
     * Converts the 'font-size' CSS style.
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
        $mmValue = sprintf( '%smm', $styleValue->value );

        $targetProperty->setAttributeNS(
            ezcDocumentOdt::NS_ODT_FO,
            "fo:{$styleName}",
            $mmValue
        );
        $targetProperty->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            "style:{$styleName}-asian",
            $mmValue
        );
        $targetProperty->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            "style:{$styleName}-complex",
            $mmValue
        );
    }
}

?>
