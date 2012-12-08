<?php
/**
 * File containing the ezcDocumentDocbookToRstParagraphHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit paragraphs
 *
 * Visit docbook paragraphs and transform them into HTML paragraphs.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstParagraphHandler extends ezcDocumentDocbookToRstBaseHandler
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
        // Find all anachors in paragraph, create pre paragraph RST anchors out
        // of them and remove them from the paragraph.
        $anchors = $node->getElementsByTagName( 'anchor' );
        $foundAnchors = false;
        foreach ( $anchors as $anchor )
        {
            $root .= '.. _' . $anchor->getAttribute( 'ID' ) . ":\n";
            $anchor->parentNode->removeChild( $anchor );
            $foundAnchors = true;
        }

        $root .= ( $foundAnchors ? "\n" : '' );

        // Visit paragraph contents
        $contents = $converter->visitChildren( $node, '' );

        // Remove all line breaks inside the paragraph.
        $contents = trim( preg_replace( '(\s+)', ' ', $contents ) );
        $root .= ezcDocumentDocbookToRstConverter::wordWrap( $contents ) . "\n\n";

        $root = $converter->finishParagraph( $root );

        return $root;
    }
}

?>
