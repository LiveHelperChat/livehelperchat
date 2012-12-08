<?php
/**
 * File containing the ezcDocumentDocbookToEzXmlParagraphHandler class.
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
class ezcDocumentDocbookToEzXmlParagraphHandler extends ezcDocumentElementVisitorHandler
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
        // Do not stack paragraphs
        if ( $root->tagName !== 'paragraph' )
        {
            $paragraph = $root->ownerDocument->createElement( 'paragraph' );
            $root->appendChild( $paragraph );
            $converter->visitChildren( $node, $paragraph );
        }
        else
        {
            $converter->visitChildren( $node, $root );
        }

        return $root;
    }
}

?>
