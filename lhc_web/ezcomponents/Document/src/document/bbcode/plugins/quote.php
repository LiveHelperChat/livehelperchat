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
 * Visitor for bbcode emphasis tags
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentBBCodeQuotePlugin extends ezcDocumentBBCodePlugin
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
        $quote = $root->ownerDocument->createElement( 'blockquote' );
        $root->appendChild( $quote );

        $attribution = null;
        if ( !empty( $node->token->parameters ) )
        {
            if ( !preg_match( '(^"(?P<attribution>.*)$")', $node->token->parameters, $match ) )
            {
                $visitor->triggerError( E_NOTICE,
                    'Attribution is required to be set in quotes.',
                    $node->token->line, $node->token->position
                );
                $attribution = $node->token->parameters;
            }
            else
            {
                $attribution = $match['attribution'];
            }
        }

        if ( $attribution )
        {
            $attribution = $root->ownerDocument->createElement( 'attribution', htmlspecialchars( $attribution ) );
            $quote->appendChild( $attribution );
        }

        $para = $root->ownerDocument->createElement( 'para' );
        $quote->appendChild( $para );

        foreach ( $node->nodes as $child )
        {
            $visitor->visitNode( $para, $child );
        }
    }
}

?>
