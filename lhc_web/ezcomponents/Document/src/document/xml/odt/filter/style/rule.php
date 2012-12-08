<?php
/**
 * File containing the ezcDocumentOdtStyleFilterRule interface.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Interface for style filter rules.
 *
 * A style filter rule must implement this interface.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
interface ezcDocumentOdtStyleFilterRule
{
    /**
     * Returns if the given $odtElement is handled by the rule.
     * 
     * @param DOMElement $odtElement 
     * @return bool
     */
    public function handles( DOMElement $odtElement );

    /**
     * Filter the given $odtElement based on the style information available 
     * through $styleInferencer.
     *
     * This method will only be called when handles returned true for the given 
     * $odtElement. The method may manipulate the $odtElement, especially its 
     * attributes, based on the style information.
     * 
     * @param DOMElement $odtElement 
     * @param ezcDocumentOdtStyleInferencer $styleInferencer
     */
    public function filter( DOMElement $odtElement, ezcDocumentOdtStyleInferencer $styleInferencer );
}

?>
