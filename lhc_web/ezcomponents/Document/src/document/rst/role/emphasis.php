<?php
/**
 * File containing the ezcDocumentRstEmphasisTextRole class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST emphasis text roles.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstEmphasisTextRole extends ezcDocumentRstTextRole implements ezcDocumentRstXhtmlTextRole
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
        $emphasis = $document->createElement( 'emphasis' );
        $root->appendChild( $emphasis );

        $this->appendText( $emphasis );
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
        $emphasis = $document->createElement( 'em' );
        $root->appendChild( $emphasis );

        $this->appendText( $emphasis );
    }
}

?>
