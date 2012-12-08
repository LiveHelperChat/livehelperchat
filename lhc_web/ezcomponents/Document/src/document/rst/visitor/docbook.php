<?php
/**
 * File containing the ezcDocumentRstDocbookVisitor class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Docbook visitor for the RST AST.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstDocbookVisitor extends ezcDocumentRstVisitor
{
    /**
     * Mapping of class names to internal visitors for the respective nodes.
     *
     * @var array
     */
    protected $complexVisitMapping = array(
        'ezcDocumentRstSectionNode'               => 'visitSection',
        'ezcDocumentRstParagraphNode'             => 'visitParagraph',
        'ezcDocumentRstTextLineNode'              => 'visitText',
        'ezcDocumentRstLiteralNode'               => 'visitText',
        'ezcDocumentRstExternalReferenceNode'     => 'visitExternalReference',
        'ezcDocumentRstReferenceNode'             => 'visitInternalFootnoteReference',
        'ezcDocumentRstAnonymousLinkNode'         => 'visitAnonymousReference',
        'ezcDocumentRstMarkupSubstitutionNode'    => 'visitSubstitutionReference',
        'ezcDocumentRstMarkupInterpretedTextNode' => 'visitInterpretedTextNode',
        'ezcDocumentRstMarkupStrongEmphasisNode'  => 'visitEmphasisMarkup',
        'ezcDocumentRstMarkupEmphasisNode'        => 'visitEmphasisMarkup',
        'ezcDocumentRstTargetNode'                => 'visitInlineTarget',
        'ezcDocumentRstBlockquoteNode'            => 'visitBlockquote',
        'ezcDocumentRstEnumeratedListListNode'    => 'visitEnumeratedList',
        'ezcDocumentRstDefinitionListNode'        => 'visitDefinitionListItem',
        'ezcDocumentRstTableNode'                 => 'visitTable',
        'ezcDocumentRstTableCellNode'             => 'visitTableCell',
        'ezcDocumentRstFieldListNode'             => 'visitFieldListItem',
        'ezcDocumentRstLineBlockNode'             => 'visitLineBlock',
        'ezcDocumentRstLineBlockLineNode'         => 'visitChildren',
        'ezcDocumentRstDirectiveNode'             => 'visitDirective',
    );

    /**
     * Direct mapping of AST node class names to docbook element names.
     *
     * @var array
     */
    protected $simpleVisitMapping = array(
        'ezcDocumentRstMarkupInlineLiteralNode' => 'literal',
        'ezcDocumentRstBulletListListNode'      => 'itemizedlist',
        'ezcDocumentRstDefinitionListListNode'  => 'variablelist',
        'ezcDocumentRstBulletListNode'          => 'listitem',
        'ezcDocumentRstEnumeratedListNode'      => 'listitem',
        'ezcDocumentRstLiteralBlockNode'        => 'literallayout',
        'ezcDocumentRstCommentNode'             => 'comment',
        'ezcDocumentRstTransitionNode'          => 'beginpage',
        'ezcDocumentRstTableHeadNode'           => 'thead',
        'ezcDocumentRstTableBodyNode'           => 'tbody',
        'ezcDocumentRstTableRowNode'            => 'row',
    );

    /**
     * Array with nodes, which can be ignored during the transformation
     * process, they only provide additional information during preprocessing.
     *
     * @var array
     */
    protected $skipNodes = array(
        'ezcDocumentRstNamedReferenceNode',
        'ezcDocumentRstAnonymousReferenceNode',
        'ezcDocumentRstSubstitutionNode',
        'ezcDocumentRstFootnoteNode',
    );

    /**
     * DOM document
     *
     * @var DOMDocument
     */
    protected $document;

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
        parent::visit( $ast );

        // Create article from AST
        $imp = new DOMImplementation();
        $dtd = $imp->createDocumentType( 'article', '-//OASIS//DTD DocBook XML V4.5//EN', 'http://www.oasis-open.org/docbook/xml/4.5/docbookx.dtd' );
        $this->document = $imp->createDocument( 'http://docbook.org/ns/docbook', '', $dtd );
        $this->document->formatOutput = true;

//        $root = $this->document->createElement( 'article' );
        $root = $this->document->createElementNs( 'http://docbook.org/ns/docbook', 'article' );
        $this->document->appendChild( $root );

        // Visit all childs of the AST root node.
        foreach ( $ast->nodes as $node )
        {
            $this->visitNode( $root, $node );
        }

        return $this->document;
    }

    /**
     * Visit single AST node
     *
     * Visit a single AST node, may be called for each node found anywhere
     * as child. The current position in the DOMDocument is passed by a
     * reference to the current DOMNode, which is operated on.
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitNode( DOMNode $root, ezcDocumentRstNode $node )
    {
        // Iterate over available visitors and use them to visit the nodes.
        foreach ( $this->complexVisitMapping as $class => $method )
        {
            if ( $node instanceof $class )
            {
                return $this->$method( $root, $node );
            }
        }

        // Check if we have a simple class to element name mapping
        foreach ( $this->simpleVisitMapping as $class => $elementName )
        {
            if ( $node instanceof $class )
            {
                $element = $this->document->createElement( $elementName );
                $root->appendChild( $element );

                if ( $node->identifier !== null )
                {
                    $element->setAttribute( 'ID', $node->identifier );
                }

                foreach ( $node->nodes as $child )
                {
                    $this->visitNode( $element, $child );
                }

                return;
            }
        }

        // Check if you should just ignore the node for rendering
        foreach ( $this->skipNodes as $class )
        {
            if ( $node instanceof $class )
            {
                return;
            }
        }

        // We could not find any valid visitor.
        throw new ezcDocumentMissingVisitorException( get_class( $node ) );
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
        $section = $this->document->createElement( 'section' );
        $section->setAttribute( 'ID', $this->calculateUniqueId( $this->nodeToString( $node->title ) ) );
        $root->appendChild( $section );

        $title = $this->document->createElement( 'title' );
        $section->appendChild( $title );

        foreach ( $node->title->nodes as $child )
        {
            $this->visitNode( $title, $child );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $section, $child );
        }
    }

    /**
     * Visit paragraph node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitParagraph( DOMNode $root, ezcDocumentRstNode $node )
    {
        if ( !in_array( $root->tagName, array( 'para', 'attribution', 'citation' ) ) )
        {
            $paragraph = $this->document->createElement( 'para' );
            $root->appendChild( $paragraph );

            if ( $node->identifier !== null )
            {
                $paragraph->setAttribute( 'ID', $node->identifier );
            }
        }
        else
        {
            $paragraph = $root;
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $paragraph, $child );
        }
    }

    /**
     * Visit text node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitText( DOMNode $root, ezcDocumentRstNode $node )
    {
        $root->appendChild(
            new DOMText( $node->token->content )
        );
    }

    /**
     * Visit children
     *
     * Just recurse into node and visit its children, ignoring the actual
     * node.
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitChildren( DOMNode $root, ezcDocumentRstNode $node )
    {
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }
    }

    /**
     * Visit interpreted text node markup
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitInterpretedTextNode( DOMNode $root, ezcDocumentRstNode $node )
    {
        // If no role is specified, just recurse
        if ( !isset( $node->role ) ||
             ( $node->role === false ) )
        {
            return $this->visitChildren( $root, $node );
        }

        try
        {
            $handlerClass = $this->rst->getRoleHandler( $node->role );
        }
        catch ( ezcDocumentRstMissingTextRoleHandlerException $e )
        {
            return $this->triggerError(
                E_WARNING, $e->getMessage(),
                null, $node->token->line, $node->token->position
            );
        }

        $roleHandler = new $handlerClass( $this->ast, $this->path, $node );
        $roleHandler->toDocbook( $this->document, $root );
    }

    /**
     * Visit emphasis markup
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitEmphasisMarkup( DOMNode $root, ezcDocumentRstNode $node )
    {
        $markup = $this->document->createElement( 'emphasis' );

        if ( $node instanceof ezcDocumentRstMarkupStrongEmphasisNode )
        {
            $markup->setAttribute( 'Role', 'strong' );
        }
        $root->appendChild( $markup );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $markup, $child );
        }
    }

    /**
     * Visit external reference node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitExternalReference( DOMNode $root, ezcDocumentRstNode $node )
    {
        if ( $node->target !== false )
        {
            $link = $this->document->createElement( 'ulink' );
            $link->setAttribute( 'url', htmlspecialchars( $node->target ) );
            $root->appendChild( $link );
        }
        elseif ( $target = $this->getNamedExternalReference( $this->nodeToString( $node ) ) )
        {
            $link = $this->document->createElement( 'ulink' );
            $link->setAttribute( 'url', htmlspecialchars( $target ) );
            $root->appendChild( $link );
        }
        else
        {
            $target = $this->hasReferenceTarget( $this->nodeToString( $node ), $node );

            $link = $this->document->createElement( 'link' );
            $link->setAttribute( 'linked', htmlspecialchars( $target ) );
            $root->appendChild( $link );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $link, $child );
        }
    }

    /**
     * Visit internal reference node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitInternalFootnoteReference( DOMNode $root, ezcDocumentRstNode $node )
    {
        $identifier = $this->tokenListToString( $node->name );
        $target = $this->hasFootnoteTarget( $identifier, $node );

        switch ( $node->footnoteType )
        {
            case ezcDocumentRstFootnoteNode::CITATION:
                // This is a citation reference footnote, which should be
                // visited differently from normal footnotes.
                $this->visitCitation( $root, $target );
                break;

            default:
                // The displayed label of a footnote may not be specified in
                // docbook, so we just add the footnote node.
                $this->visitFootnote( $root, $target );
        }
    }

    /**
     * Visit anonomyous reference node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitAnonymousReference( DOMNode $root, ezcDocumentRstNode $node )
    {
        $target = $node->target !== false ? $node->target : $this->getAnonymousReferenceTarget();

        $link = $this->document->createElement( 'ulink' );
        $link->setAttribute( 'url', htmlspecialchars( $target ) );
        $root->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $link, $child );
        }
    }

    /**
     * Visit substitution reference node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitSubstitutionReference( DOMNode $root, ezcDocumentRstNode $node )
    {
        if ( ( $substitution = $this->substitute( $this->nodeToString( $node ) ) ) !== null )
        {
            foreach ( $substitution as $child )
            {
                $this->visitNode( $root, $child );
            }
        }
    }

    /**
     * Visit inline target node
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitInlineTarget( DOMNode $root, ezcDocumentRstNode $node )
    {
        $link = $this->document->createElement( 'anchor' );
        $link->setAttribute( 'ID', $this->calculateId( $this->nodeToString( $node ) ) );
        $root->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }
    }

    /**
     * Visit citation
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitCitation( DOMNode $root, ezcDocumentRstNode $node )
    {
        $footnote = $this->document->createElement( 'citation' );
        $root->appendChild( $footnote );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $footnote, $child );
        }
    }

    /**
     * Visit footnote
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitFootnote( DOMNode $root, ezcDocumentRstNode $node )
    {
        $footnote = $this->document->createElement( 'footnote' );
        $root->appendChild( $footnote );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $footnote, $child );
        }
    }

    /**
     * Visit blockquotes
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitBlockquote( DOMNode $root, ezcDocumentRstNode $node )
    {
        $quote = $this->document->createElement( 'blockquote' );
        $root->appendChild( $quote );

        // Add blockquote attribution
        if ( !empty( $node->annotation ) )
        {
            $attribution = $this->document->createElement( 'attribution' );
            $quote->appendChild( $attribution );
            $this->visitNode( $attribution, $node->annotation->nodes );
        }

        // Decoratre blockquote contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $quote, $child );
        }
    }

    /**
     * Visit enumerated lists
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitEnumeratedList( DOMNode $root, ezcDocumentRstNode $node )
    {
        $list = $this->document->createElement( 'orderedlist' );

        $enumerationTypeMapping = array(
            ezcDocumentRstEnumeratedListNode::NUMERIC     => 'arabic',
            ezcDocumentRstEnumeratedListNode::LOWER_ROMAN => 'lowerroman',
            ezcDocumentRstEnumeratedListNode::UPPER_ROMAN => 'upperroman',
            ezcDocumentRstEnumeratedListNode::LOWERCASE   => 'loweralpha',
            ezcDocumentRstEnumeratedListNode::UPPERCASE   => 'upperalpha',
        );

        // Detect enumeration type
        if ( isset( $enumerationTypeMapping[$node->nodes[0]->listType] ) )
        {
            $list->setAttribute( 'numeration', $enumerationTypeMapping[$node->nodes[0]->listType] );
        }

        $root->appendChild( $list );

        // Visit list contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $list, $child );
        }
    }

    /**
     * Visit definition list item
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitDefinitionListItem( DOMNode $root, ezcDocumentRstNode $node )
    {
        $item = $this->document->createElement( 'varlistentry' );
        $root->appendChild( $item );

        $term = $this->document->createElement( 'term', htmlspecialchars( $this->tokenListToString( $node->name ) ) );
        $item->appendChild( $term );

        $definition = $this->document->createElement( 'listitem' );
        $item->appendChild( $definition );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $definition, $child );
        }
    }

    /**
     * Visit line block
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitLineBlock( DOMNode $root, ezcDocumentRstNode $node )
    {
        $para = $this->document->createElement( 'literallayout' );
        $para->setAttribute( 'class', 'normal' );
        $root->appendChild( $para );

        // Visit lines
        foreach ( $node->nodes as $child )
        {
            foreach ( $child->nodes as $literal )
            {
                $para->appendChild( new DOMText(
                    ( $literal->token->type !== ezcDocumentRstToken::NEWLINE ) ? $literal->token->content : ' '
                ) );
            }
            $para->appendChild( new DOMText( "\n" ) );
        }
    }

    /**
     * Visit table
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitTable( DOMNode $root, ezcDocumentRstNode $node )
    {
        $table = $this->document->createElement( 'table' );
        $root->appendChild( $table );

        $group = $this->document->createElement( 'tgroup' );
        $table->appendChild( $group );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $group, $child );
        }
    }

    /**
     * Visit table cell
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitTableCell( DOMNode $root, ezcDocumentRstNode $node )
    {
        $cell = $this->document->createElement( 'entry' );
        $root->appendChild( $cell );

        // @todo: Colspans may be generated by spanspecs, like shown here:
        // http://www.oasis-open.org/docbook/documentation/reference/html/table.html
        //
        // Or we may just use "HTML tables", which support all the required
        // features and are at least also available in docbook 4.5
        if ( $node->rowspan > 1 )
        {
            $cell->setAttribute( 'morerows', $node->rowspan - 1 );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $cell, $child );
        }
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
        // Get sectioninfo node, to add the stuff there.
        $secInfo = $root->getElementsByTagName( 'sectioninfo' )->item( 0 );

        if ( $secInfo === null )
        {
            // If not yet existant, create section info
            $secInfo = $root->ownerDocument->createElement( 'sectioninfo' );
            $root->insertBefore( $secInfo, $root->firstChild );
        }

        $fieldListItemMapping = array(
            'authors'     => 'authors',
            'description' => 'abstract',
            'copyright'   => 'copyright',
            'version'     => 'releaseinfo',
            'date'        => 'date',
            'author'      => 'author',
        );

        $fieldName = strtolower( trim( $this->tokenListToString( $node->name ) ) );
        if ( !isset( $fieldListItemMapping[$fieldName] ) )
        {
            return $this->triggerError(
                E_NOTICE, "Unhandeled field list type '{$fieldName}'.",
                null, $node->token->line, $node->token->position
            );
        }

        $item = $this->document->createElement(
            $fieldListItemMapping[$fieldName],
            htmlspecialchars( $this->nodeToString( $node ) )
        );
        $secInfo->appendChild( $item );
    }

    /**
     * Visit directive
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitDirective( DOMNode $root, ezcDocumentRstNode $node )
    {
        try
        {
            $handlerClass = $this->rst->getDirectiveHandler( $node->identifier );
        }
        catch ( ezcDocumentRstMissingDirectiveHandlerException $e )
        {
            return $this->triggerError(
                E_WARNING, $e->getMessage(),
                null, $node->token->line, $node->token->position
            );
        }

        $directiveHandler = new $handlerClass( $this->ast, $this->path, $node );
        $directiveHandler->toDocbook( $this->document, $root );
    }
}

?>
