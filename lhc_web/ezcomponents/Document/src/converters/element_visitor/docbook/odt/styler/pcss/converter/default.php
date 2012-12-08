<?php
/**
 * File containing the ezcDocumentOdtDefaultPcssConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Default style converter which converts just to "fo:$stylename".
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtDefaultPcssConverter implements ezcDocumentOdtPcssConverter
{
    /**
     * Converts CSS styles directly without value conversion.
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
            ezcDocumentOdt::NS_ODT_FO,
            "fo:{$styleName}",
            $styleValue->value
        );
    }
}

?>
