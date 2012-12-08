<?php
/**
 * File containing the ezcDocumentEzXmlToDocbookLineHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit eZXml line elements.
 *
 * Line elements are used to enforce breakes inside text.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlToDocbookLineHandler extends ezcDocumentElementVisitorHandler
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
        $element = $root->ownerDocument->createElement( 'literallayout' );
        $element->setAttribute( 'class', 'normal' );
        $root->parentNode->appendChild( $element );

        // Recurse
        $converter->visitChildren( $node, $element );

        // Aggregate additional line block elements
        if ( $node->nextSibling &&
             ( ( ( $node->nextSibling->nodeType === XML_ELEMENT_NODE ) &&
                 ( $node->nextSibling->tagName === 'line' ) ) ||
               ( ( $node->nextSibling->nodeType === XML_TEXT_NODE ) &&
                 ( trim( $node->nextSibling->data ) === '' ) ) ) )
        {
            do {
                if ( $node->nextSibling->nodeType === XML_ELEMENT_NODE )
                {
                    $element->appendChild( new DOMText( "\n" ) );
                    $converter->visitChildren( $node->nextSibling, $element );
                }

                $node->parentNode->removeChild( $node->nextSibling );
            } while ( $node->nextSibling &&
                      ( ( ( $node->nextSibling->nodeType === XML_ELEMENT_NODE ) &&
                          ( $node->nextSibling->tagName === 'line' ) ) ||
                        ( ( $node->nextSibling->nodeType === XML_TEXT_NODE ) &&
                          ( trim( $node->nextSibling->data ) === '' ) ) ) );
        }

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
        return $root;
    }
}

?>
