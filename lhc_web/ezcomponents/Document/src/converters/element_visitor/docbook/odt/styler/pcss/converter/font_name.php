<?php
/**
 * File containing the ezcDocumentOdtPcssFontNameConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style converter for the special font-name style property.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtPcssFontNameConverter implements ezcDocumentOdtPcssConverter
{
    /**
     * Converts the special 'font-name' CSS style property.
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
        $targetProperty->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            "style:{$styleName}",
            $styleValue->value
        );
        $targetProperty->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            "style:{$styleName}-asian",
            $styleValue->value
        );
        $targetProperty->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            "style:{$styleName}-complex",
            $styleValue->value
        );
    }
}

?>
