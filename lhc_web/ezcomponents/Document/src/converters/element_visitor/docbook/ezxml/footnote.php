<?php
/**
 * File containing the ezcDocumentDocbookToEzXmlFootnoteHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit footnotes
 *
 * Footnotes in docbook are emebdded at the position, the reference should
 * occur. We store the contents, to be rendered at the end of the HTML
 * document, and only render a number referencing the actual footnote at
 * the position of the footnote in the docbook document.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlFootnoteHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * Handle a node
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @param mixed $root
     * @return mixed
     */
    public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root )
    {
        $number = $converter->appendFootnote( $node->cloneNode( true ) );

        $footnoteReference = $root->ownerDocument->createElement( 'link', $number );
        $footnoteReference->setAttribute( 'class', 'footnote' );
        $footnoteReference->setAttribute( 'anchor_name', '__footnote_' . $number );
        $root->appendChild( $footnoteReference );

        return $root;
    }
}

?>
