<?php
/**
 * File containing the ezcDocumentRstFigureDirective class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST image directives
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstFigureDirective extends ezcDocumentRstImageDirective
{
    /**
     * Transform directive to docbook
     *
     * Create a docbook XML structure at the directives position in the
     * document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toDocbook( DOMDocument $document, DOMElement $root )
    {
        parent::toDocbook( $document, $root );

        $text = '';
        foreach ( $this->node->nodes as $node )
        {
            $text .= $node->token->content;
        }
        $text = trim( $text );

        if ( !empty( $text ) )
        {
            $media = $root->getElementsBytagName( 'mediaobject' )->item( 0 );
            $caption = $document->createElement( 'caption' );
            $media->appendChild( $caption );

            $paragraph = $document->createElement( 'para', htmlspecialchars( $text ) );
            $caption->appendChild( $paragraph );
        }
    }

    /**
     * Transform directive to HTML
     *
     * Create a XHTML structure at the directives position in the document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toXhtml( DOMDocument $document, DOMElement $root )
    {
        $box = $document->createElement( 'div' );
        $box->setAttribute( 'class', 'figure' );
        $root->appendChild( $box );

        parent::toXhtml( $document, $box );

        $text = '';
        foreach ( $this->node->nodes as $node )
        {
            $text .= $node->token->content;
        }
        $text = trim( $text );

        $paragraph = $document->createElement( 'p', htmlspecialchars( $text ) );
        $box->appendChild( $paragraph );
    }
}

?>
