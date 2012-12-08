<?php
/**
 * File containing the ezcDocumentBBCodePlugin class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for bbcode email tags
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentBBCodeEmailPlugin extends ezcDocumentBBCodePlugin
{
    /**
     * Convert a BBCode tag into Docbook
     *
     * Convert the given node into a Docbook structure, in the given root. For 
     * child elements in the node you may call the visitNode() method of the 
     * provided visitor.
     *
     * @param ezcDocumentBBCodeVisitor $visitor 
     * @param DOMElement $root 
     * @param ezcDocumentBBCodeNode $node 
     * @return void
     */
    public function toDocbook( ezcDocumentBBCodeVisitor $visitor, DOMElement $root, ezcDocumentBBCodeNode $node )
    {
        if ( $node->token->parameters !== null )
        {
            // The actual URL is a parameter
            $url = $root->ownerDocument->createElement( 'ulink' );
            $url->setAttribute( 'url', 'mailto:' . $node->token->parameters );
            $root->appendChild( $url );

            foreach ( $node->nodes as $child )
            {
                $visitor->visitNode( $url, $child );
            }
        }
        else
        {
            // The URL is the contained text of the link
            $url = $root->ownerDocument->createElement( 'ulink' );
            $root->appendChild( $url );

            foreach ( $node->nodes as $child )
            {
                $visitor->visitNode( $url, $child );
            }

            $url->setAttribute( 'url', 'mailto:' . $url->textContent );
        }
    }
}

?>
