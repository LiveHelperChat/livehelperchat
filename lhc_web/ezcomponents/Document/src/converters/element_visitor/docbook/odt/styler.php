<?php
/**
 * File containing the ezcDocumentOdtStyler interface.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for ODT stylers.
 *
 * This interface must be implemented by stylers provided in the {@link 
 * ezcDocumentDocbookToOdtConverterOptions}.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 */
interface ezcDocumentOdtStyler
{
    /**
     * Initialize the styler with the given $odtDocument.
     *
     * This method *must* be called *before* {@link applyStyles()} is called 
     * at all. Otherwise an exception will be thrown. This method is called by 
     * the {@link ezcDocumentDocbookToOdtConverter} whenever a new ODT document 
     * is to be converted.
     * 
     * @param DOMDocument $odtDocument
     */
    public function init( DOMDocument $odtDocument );

    /**
     * Applies the style information associated with $docBookElement to 
     * $odtElement.
     *
     * This method must apply the style information associated with the given 
     * $docBookElement to the $odtElement given.
     * 
     * @param ezcDocumentLocateable $docBookElement 
     * @param DOMElement $odtElement 
     * @throws ezcDocumentOdtStylerNotInitializedException
     *         if the styler has not been initialized using the {@link init()} 
     *         method, yet. Initialization is performed in the {@link 
     *         ezcDocumentDocbookToOdtConverter}.
     */
    public function applyStyles( ezcDocumentLocateable $docBookElement, DOMElement $odtElement );
}

?>
