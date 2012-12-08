<?php
/**
 * File containing the ezcDocumentRstStrongTextRole class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST strong text roles.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstStrongTextRole extends ezcDocumentRstTextRole implements ezcDocumentRstXhtmlTextRole
{
    /**
     * Transform text role to docbook.
     *
     * Create a docbook XML structure at the text roles position in the
     * document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     */
    public function toDocbook( DOMDocument $document, DOMElement $root )
    {
        $strong = $document->createElement( 'emphasis' );
        $strong->setAttribute( 'Role', 'strong' );
        $root->appendChild( $strong );

        $this->appendText( $strong );
    }

    /**
     * Transform text role to HTML.
     *
     * Create a XHTML structure at the text roles position in the document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     */
    public function toXhtml( DOMDocument $document, DOMElement $root )
    {
        $strong = $document->createElement( 'strong' );
        $root->appendChild( $strong );

        $this->appendText( $strong );
    }
}

?>
