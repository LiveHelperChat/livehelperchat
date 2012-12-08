<?php
/**
 * File containing the ezcDocumentDocbookToEzXmlLiteralLayoutHandler class.
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
 * structures. Code blocks will be transformed into 'pre' elements, while
 * poem like texts will be handled by a 'p' element, in which each line is
 * seperated by 'br' elements.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlLiteralLayoutHandler extends ezcDocumentElementVisitorHandler
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
            $paragraph = $root->ownerDocument->createElement( 'paragraph' );
            $root->appendChild( $paragraph );

            $literal = $root->ownerDocument->createElement( 'literal' );
            $paragraph->appendChild( $literal );

            $converter->visitChildren( $node, $literal );
        }
        else
        {
            $paragraph = $root->ownerDocument->createElement( 'paragraph' );

            $textLines = preg_split( '(\r\n|\r|\n)', $node->textContent );
            foreach ( $textLines as $line )
            {
                // Replace space by non-breaking spaces, as this is how it is
                // supposed to be rendered.
                $line = $root->ownerDocument->createElement( 'line', htmlspecialchars( str_replace( ' ', "\xc2\xa0", $line ) ) );
                $paragraph->appendChild( $line );
            }

            $root->appendChild( $paragraph );
        }

        return $root;
    }
}

?>
