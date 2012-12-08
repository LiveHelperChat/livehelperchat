<?php
/**
 * File containing the ezcDocumentOdtPcssPreprocessor interface.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * PCSS style preprocessor interface.
 *
 * Style pre-processors hook into the {@link ezcDocumentOdtStyler} right after 
 * the styles for an element have been determined and right before the 
 * corresponding style information is generated in the ODT document and applied 
 * to the ODT element. Pre-processors may generate styling information which is 
 * not provided by PCSS or stored in other ways as well as unify styles and 
 * process new styles from existing ones.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 */
interface ezcDocumentOdtPcssPreprocessor
{
    /**
     * Pre-process styles and return them.
     *
     * This method may pre-process the $styles generated from the 
     * $docBookElement for the given $odtElement. The processing may include 
     * creation of new style attributes and manipulation of style attributes.  
     * Removal of style attributes is discouraged!
     * 
     * In addition, a pre-processor may utilize the DOMElements from the $styleInfo struct 
     * to extract additional information needed or to perform style related DOM 
     * manipultations.
     *
     * @param ezcDocumentOdtStyleInformation $styleInfo
     * @param DOMElement $docBookElement
     * @param DOMElement $odtElement 
     * @param array $styles 
     * @return array
     */
    function process( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $docBookElement, DOMElement $odtElement, array $styles );
}

?>
