<?php
/**
 * File containing the ezcDocumentDocbookToHtmlLiteralLayoutHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit literallayout elements
 *
 * Literallayout elements are used for code blocks in docbook, where
 * normally some fixed width font is used, but also for poems or simliarly
 * formatted texts. In HTML those are represented by entirely different
 * structures. Code blocks will be transformed into <pre> elements, while
 * poem like texts will be handled by a <p> element, in which each line is
 * seperated by <br> elements.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlLiteralLayoutHandler extends ezcDocumentDocbookToHtmlBaseHandler
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
        if ( !$node->hasAttribute( 'class' ) ||
             ( $node->getAttribute( 'class' ) !== 'normal' ) )
        {
            // This is "just" a code block
            $code = $root->ownerDocument->createElement( 'pre' );
            $root->appendChild( $code );
            $converter->visitChildren( $node, $code );
        }
        else
        {
            $paragraph = $root->ownerDocument->createElement( 'p' );
            $paragraph->setAttribute( 'class', 'lineblock' );

            $textLines = preg_split( '(\r\n|\r|\n)', $node->textContent );
            foreach ( $textLines as $line )
            {
                // Replace space by non-breaking spaces, as this is how it is
                // supposed to be rendered.
                $line = new DOMText( str_replace( ' ', "\xc2\xa0", $line ) );
                $paragraph->appendChild( $line );

                $break = $root->ownerDocument->createElement( 'br' );
                $paragraph->appendChild( $break );
            }

            $root->appendChild( $paragraph );
        }

        return $root;
    }
}

?>
