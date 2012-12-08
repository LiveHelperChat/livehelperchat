<?php
/**
 * File containing the ezcDocumentRstXhtmlTextRole interface.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for text roles also supporting HTML output
 *
 * @package Document
 * @version 1.3.1
 */
interface ezcDocumentRstXhtmlTextRole
{
    /**
     * Transform text role to HTML
     *
     * Create a XHTML structure at the text roles position in the document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toXhtml( DOMDocument $document, DOMElement $root );
}

?>
