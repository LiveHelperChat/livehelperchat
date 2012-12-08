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
 * Visitor for bbcode plugins
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentBBCodePlugin
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
    abstract public function toDocbook( ezcDocumentBBCodeVisitor $visitor, DOMElement $root, ezcDocumentBBCodeNode $node );

    /**
     * Recursively extract text from node
     * 
     * @param ezcDocumentBBCodeNode $node 
     * @return void
     */
    protected function getText( ezcDocumentBBCodeNode $node )
    {
        $text = '';

        foreach ( $node->nodes as $child )
        {
            if ( $child instanceof ezcDocumentBBCodeTextNode )
            {
                $text .= $child->token->content;
            }
            elseif ( is_array( $child->nodes ) )
            {
                $text .= $this->getText( $child );
            }
        }

        return $text;
    }
}

?>
