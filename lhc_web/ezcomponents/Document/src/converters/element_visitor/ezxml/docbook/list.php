<?php
/**
 * File containing the ezcDocumentEzXmlToDocbookListHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit eZXml lists.
 *
 * Lists are children of paragraphs in eZXml, but are part of the section nodes
 * in docbook. The containing paragraph needs to be split up and the list node
 * moved to the top level node.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlToDocbookListHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * Handle a node.
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
        $element = $root->ownerDocument->createElement( $node->tagName === 'ul' ? 'itemizedlist' : 'orderedlist' );
        $root->parentNode->appendChild( $element );

        // If there are any siblings, put them into a new paragraph node,
        // "below" the list node.
        if ( $node->nextSibling )
        {
            $newParagraph = $node->ownerDocument->createElement( 'paragraph' );

            do {
                $newParagraph->appendChild( $node->nextSibling->cloneNode( true ) );
                $node->parentNode->removeChild( $node->nextSibling );
            } while ( $node->nextSibling );

            $node->parentNode->parentNode->appendChild( $newParagraph );
        }

        // Recurse
        $converter->visitChildren( $node, $element );
        return $root;
    }
}

?>
