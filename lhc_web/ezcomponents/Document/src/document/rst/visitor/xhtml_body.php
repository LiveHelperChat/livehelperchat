<?php
/**
 * File containing the ezcDocumentRstXhtmlBodyVisitor class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * HTML visitor for the RST AST, which only produces contents to be embedded
 * somewhere into the body of an existing HTML document.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstXhtmlBodyVisitor extends ezcDocumentRstXhtmlVisitor
{
    /**
     * Docarate RST AST
     *
     * Visit the RST abstract syntax tree.
     *
     * @param ezcDocumentRstDocumentNode $ast
     * @return mixed
     */
    public function visit( ezcDocumentRstDocumentNode $ast )
    {
        // There is no parent::parent::, so this is the dublicated code from
        // ezcDocumentRstVisitor::visit() method.
        $this->ast = $ast;
        $this->preProcessAst( $ast );

        // Reset footnote counters
        foreach ( $this->footnoteCounter as $label => $counter )
        {
            $this->footnoteCounter[$label] = 0;
        }

        // Set title level from options
        $this->depth = $this->options->headerLevel - 1;

        // Create article from AST
        $this->document = new DOMDocument();
        $this->document->formatOutput = true;

        $root = $this->document->createElement( 'div' );
        $root->setAttribute( 'class', 'article' );
        $this->document->appendChild( $root );

        $body = $this->document->createElement( 'div' );
        $body->setAttribute( 'class', 'body' );
        $root->appendChild( $body );

        // Visit all childs of the AST root node.
        foreach ( $ast->nodes as $node )
        {
            $this->visitNode( $body, $node );
        }

        // Visit all footnotes at the document body
        foreach ( $this->footnotes as $footnotes )
        {
            ksort( $footnotes );
            $footnoteList = $this->document->createElement( 'ul' );
            $footnoteList->setAttribute( 'class', 'footnotes' );
            $body->appendChild( $footnoteList );

            foreach ( $footnotes as $footnote )
            {
                $this->visitFootnote( $footnoteList, $footnote );
            }
        }

        return $this->document;
    }

    /**
     * Visit section node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitSection( DOMNode $root, ezcDocumentRstNode $node )
    {
        $header = $this->document->createElement( 'h' . min( 6, ++$this->depth ) );
        $root->appendChild( $header );

        if ( $this->depth >= 6 )
        {
            $header->setAttribute( 'class', 'h' . $this->depth );
        }

        $reference = $this->document->createElement( 'a' );
        $reference->setAttribute( 'name', $this->calculateId( $this->nodeToString( $node->title ) ) );
        $header->appendChild( $reference );

        foreach ( $node->title->nodes as $child )
        {
            $this->visitNode( $header, $child );
        }

        if ( $this->head === null )
        {
            $this->head = $this->document->createElement( 'dl' );
            $this->head->setAttribute( 'class', 'head' );
            $root->appendChild( $this->head );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }

        --$this->depth;
    }

    /**
     * Visit field list item
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitFieldListItem( DOMNode $root, ezcDocumentRstNode $node )
    {
        $fieldName = strtolower( trim( $this->tokenListToString( $node->name ) ) );
        $term = $this->document->createElement( 'dt', htmlspecialchars( $fieldName ) );
        $this->head->appendChild( $term );

        $term = $this->document->createElement( 'dd', htmlspecialchars( trim( $this->nodeToString( $node ) ) ) );
        $this->head->appendChild( $term );
    }
}

?>
