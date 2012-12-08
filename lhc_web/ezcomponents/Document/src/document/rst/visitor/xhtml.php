<?php
/**
 * File containing the ezcDocumentRstXhtmlVisitor class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * HTML visitor for the RST AST.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstXhtmlVisitor extends ezcDocumentRstVisitor
{
    /**
     * Mapping of class names to internal visitors for the respective nodes.
     *
     * @var array
     */
    protected $complexVisitMapping = array(
        'ezcDocumentRstSectionNode'               => 'visitSection',
        'ezcDocumentRstTextLineNode'              => 'visitText',
        'ezcDocumentRstMarkupInterpretedTextNode' => 'visitInterpretedTextNode',
        'ezcDocumentRstExternalReferenceNode'     => 'visitExternalReference',
        'ezcDocumentRstMarkupSubstitutionNode'    => 'visitSubstitutionReference',
        'ezcDocumentRstTargetNode'                => 'visitInlineTarget',
        'ezcDocumentRstAnonymousLinkNode'         => 'visitAnonymousReference',
        'ezcDocumentRstBlockquoteNode'            => 'visitBlockquote',
        'ezcDocumentRstBulletListListNode'        => 'visitBulletList',
        'ezcDocumentRstEnumeratedListListNode'    => 'visitEnumeratedList',
        'ezcDocumentRstReferenceNode'             => 'visitInternalFootnoteReference',
        'ezcDocumentRstLineBlockNode'             => 'visitLineBlock',
        'ezcDocumentRstLineBlockLineNode'         => 'visitLineBlockLine',
        'ezcDocumentRstLiteralNode'               => 'visitText',
        'ezcDocumentRstCommentNode'               => 'visitComment',
        'ezcDocumentRstDefinitionListNode'        => 'visitDefinitionListItem',
        'ezcDocumentRstTableCellNode'             => 'visitTableCell',
        'ezcDocumentRstFieldListNode'             => 'visitFieldListItem',
        'ezcDocumentRstDirectiveNode'             => 'visitDirective',
    );

    /**
     * Direct mapping of AST node class names to docbook element names.
     *
     * @var array
     */
    protected $simpleVisitMapping = array(
        'ezcDocumentRstParagraphNode'            => 'p',
        'ezcDocumentRstMarkupEmphasisNode'       => 'em',
        'ezcDocumentRstMarkupStrongEmphasisNode' => 'strong',
        'ezcDocumentRstMarkupInlineLiteralNode'  => 'code',
        'ezcDocumentRstBulletListNode'           => 'li',
        'ezcDocumentRstEnumeratedListNode'       => 'li',
        'ezcDocumentRstLiteralBlockNode'         => 'pre',
        'ezcDocumentRstTransitionNode'           => 'hr',
        'ezcDocumentRstDefinitionListListNode'   => 'dl',
        'ezcDocumentRstTableNode'                => 'table',
        'ezcDocumentRstTableHeadNode'            => 'thead',
        'ezcDocumentRstTableBodyNode'            => 'tbody',
        'ezcDocumentRstTableRowNode'             => 'tr',
        /*
        'ezcDocumentRstMarkupInlineLiteralNode' => 'literal',
        */
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
     * Reference to head node
     *
     * @var DOMElement
     */
    protected $head;

    /**
     * Current depth in document.
     *
     * @var int
     */
    protected $depth = 0;

    /**
     * HTML rendering options
     *
     * @var ezcDocumentHtmlConverterOptions
     */
    protected $options;

    /**
     * Create visitor from RST document handler.
     *
     * @param ezcDocumentRst $document
     * @param string $path
     * @return void
     */
    public function __construct( ezcDocumentRst $document, $path )
    {
        $this->options = new ezcDocumentHtmlConverterOptions();
        parent::__construct( $document, $path );
    }

    /**
     * Property get access.
     * Simply returns a given option.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @param string $propertyName The name of the option to get.
     * @return mixed The option value.
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     */
    public function __get( $propertyName )
    {
        if ( $propertyName === 'options' );
        {
            return $this->options;
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Sets an option.
     * This method is called when an option is set.
     *
     * @param string $propertyName  The name of the option to set.
     * @param mixed $propertyValue The option value.
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        if ( $propertyName === 'options' )
        {
            if ( $propertyValue instanceof ezcDocumentHtmlConverterOptions )
            {
                return $this->options = $propertyValue;
            }
            else
            {
                throw new ezcBaseValueException( $name, $value, 'ezcDocumentHtmlConverterOptions' );
            }
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Returns if a option exists.
     *
     * @param string $propertyName Option name to check for.
     * @return bool Whether the option exists.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return ( $propertyName === 'options' );
    }

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
        $dtd = $imp->createDocumentType( 'html', '-//W3C//DTD XHTML 1.0 Transitional//EN', 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd' );
        $this->document = $imp->createDocument( 'http://www.w3.org/1999/xhtml', '', $dtd );

        $root = $this->document->createElementNs( 'http://www.w3.org/1999/xhtml', 'html' );
        $this->document->appendChild( $root );

        $this->head = $this->document->createElement( 'head' );
        $root->appendChild( $this->head );

        // Append generator
        $generator = $this->document->createElement( 'meta' );
        $generator->setAttribute( 'name', 'generator' );
        $generator->setAttribute( 'content', 'eZ Components; http://ezcomponents.org' );
        $this->head->appendChild( $generator );

        // Set content type and encoding
        $type = $this->document->createElement( 'meta' );
        $type->setAttribute( 'http-equiv', 'Content-Type' );
        $type->setAttribute( 'content', 'text/html; charset=utf-8' );
        $this->head->appendChild( $type );

        $this->addStylesheets( $this->head );

        $body = $this->document->createElement( 'body' );
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

        // Check that all required elements for a valid XHTML document exist
        if ( $this->head->getElementsByTagName( 'title' )->length < 1 )
        {
            $title = $this->document->createElement( 'title', 'Empty document' );
            $this->head->appendChild( $title );
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
//                    $element->setAttribute( 'id', $node->identifier );
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
     * Add stylesheets to header
     *
     * @param DOMElement $head
     * @return void
     */
    protected function addStylesheets( DOMElement $head )
    {
        if ( $this->options->styleSheets !== null )
        {
            foreach ( $this->options->styleSheets as $styleSheet )
            {
                $link = $this->document->createElement( 'link' );
                $link->setAttribute( 'rel', 'Stylesheet' );
                $link->setAttribute( 'type', 'text/css' );
                $link->setAttribute( 'href', htmlspecialchars( $styleSheet ) );
                $head->appendChild( $link );
            }
        }
        else
        {
            $style = $this->document->createElement( 'style', htmlspecialchars( $this->options->styleSheet ) );
            $style->setAttribute( 'type', 'text/css' );
            $head->appendChild( $style );
        }
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
        if ( $this->depth <= 0 )
        {
            // Set document title from section
            $title = $this->document->createElement(
                'title',
                htmlspecialchars( $this->nodeToString( $node->title ) )
            );
            $this->head->appendChild( $title );
        }

        $header = $this->document->createElement( 'h' . min( 6, ++$this->depth ) );
        $root->appendChild( $header );

        if ( $this->depth >= 6 )
        {
            $header->setAttribute( 'class', 'h' . $this->depth );
        }

        $reference = $this->document->createElement( 'a' );
        $reference->setAttribute( 'name', htmlspecialchars( $node->reference ) );
        $header->appendChild( $reference );

        foreach ( $node->title->nodes as $child )
        {
            $this->visitNode( $header, $child );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }

        --$this->depth;
    }

    /**
     * Helper function for URL escaping
     *
     * Escapes and returns the first value in a match array
     *
     * @param array $values
     * @ignore
     * @return string
     */
    protected static function urlEscapeArray( array $values )
    {
        return urlencode( $values[0] );
    }

    /**
     * Escape all special characters in URIs
     *
     * @param string $url
     * @return string
     */
    public function escapeUrl( $url )
    {
        return preg_replace_callback(
            '([^a-z0-9._:/#&?@=-]+)',
            array( 'ezcDocumentRstXhtmlVisitor', 'urlEscapeArray' ),
            $url
        );
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
        if ( !$roleHandler instanceof ezcDocumentRstXhtmlTextRole )
        {
            return $this->triggerError(
                E_WARNING, "Directive '{$handlerClass}' does not support HTML rendering.",
                null, $node->token->line, $node->token->position
            );
        }
        $roleHandler->toXhtml( $this->document, $root );
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
            $linkUrl = $this->escapeUrl( $node->target );
        }
        elseif ( ( $target = $this->getNamedExternalReference( $this->nodeToString( $node ) ) ) !== false )
        {
            $linkUrl = $this->escapeUrl( $target );
        }
        else
        {
            $target = $this->hasReferenceTarget( $this->nodeToString( $node ), $node );
            $linkUrl = '#' . $this->escapeUrl( $target );
        }

        $link = $this->document->createElement( 'a' );
        $link->setAttribute( 'href', $linkUrl );
        $root->appendChild( $link );

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

        // The displayed label of a footnote may not be specified in
        // docbook, so we just add the footnote node.
        $link = $this->document->createElement( 'a', $target->number );
        $link->setAttribute( 'href', '#' . $this->generateFootnoteReferenceLink( $target->name, $target->number ) );
        $link->setAttribute( 'class', 'footnote' );
        $root->appendChild( $link );
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
        $link = $this->document->createElement( 'a' );
        $link->setAttribute( 'name', $this->calculateId( $this->nodeToString( $node ) ) );
        $root->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $link, $child );
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

        $link = $this->document->createElement( 'a' );
        $link->setAttribute( 'href', $this->escapeUrl( $target ) );
        $root->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $link, $child );
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

        // Decoratre blockquote contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $quote, $child );
        }

        // Add blockquote annotation
        if ( !empty( $node->annotation ) )
        {
            $attribution = $this->document->createElement( 'div' );
            $attribution->setAttribute( 'class', 'attribution' );
            $quote->appendChild( $attribution );

            $cite = $this->document->createElement( 'cite' );
            $attribution->appendChild( $cite );

            $this->visitChildren( $cite, $node->annotation->nodes );
        }
    }

    /**
     * Visit bullet lists
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitBulletList( DOMNode $root, ezcDocumentRstNode $node )
    {
        $list = $this->document->createElement( 'ul' );
        $root->appendChild( $list );

        $listTypes = array(
            '*'            => 'circle',
            '+'            => 'disc',
            '-'            => 'square',
            "\xe2\x80\xa2" => 'disc',
            "\xe2\x80\xa3" => 'circle',
            "\xe2\x81\x83" => 'square',
        );
        // Not allowed in XHtml strict
        // $list->setAttribute( 'type', $listTypes[$node->token->content] );

        // Decoratre blockquote contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $list, $child );
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
        $list = $this->document->createElement( 'ol' );

        // Detect enumeration type
        /* Not allowed in XHtml strict
        switch ( true )
        {
            case preg_match( '(^m{0,4}d?c{0,3}l?x{0,3}v{0,3}i{0,3}v?x?l?c?d?m?$)', $node->token->content ):
                $list->setAttribute( 'type', 'i' );
                break;

            case preg_match( '(^M{0,4}D?C{0,3}L?X{0,3}V{0,3}I{0,3}V?X?L?C?D?M?$)', $node->token->content ):
                $list->setAttribute( 'type', 'I' );
                break;

            case preg_match( '(^[a-z]$)', $node->token->content ):
                $list->setAttribute( 'type', 'a' );
                break;

            case preg_match( '(^[A-Z]$)', $node->token->content ):
                $list->setAttribute( 'type', 'A' );
                break;
        }
        // */

        $root->appendChild( $list );

        // Visit list contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $list, $child );
        }
    }

    /**
     * Generate footnote reference link
     *
     * Generate an internal target name out of the footnote name, which may
     * contain special characters, which are not allowed for URL anchors and
     * are converted to alphanumeric strings by this method.
     *
     * @param string $name
     * @param string $number
     * @return string
     */
    protected function generateFootnoteReferenceLink( $name, $number )
    {
        return '_footnote_' . str_replace(
            $this->footnoteSymbols,
            array(
                'asterisk',
                'dagger',
                'double_dagger',
                'section_mark',
                'pilcrow',
                'number_sign',
                'spade',
                'heart',
                'diamond',
                'club',
            ),
            $name . '_' . $number
        );
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
        $footnote = $this->document->createElement( 'li' );
        $root->appendChild( $footnote );

        $link = $this->document->createElement( 'a', $node->number );
        $link->setAttribute( 'name', $this->generateFootnoteReferenceLink( $node->name, $node->number ) );
        $footnote->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $footnote, $child );
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
        $block = $this->document->createElement( 'p' );
        $block->setAttribute( 'class', 'lineblock' );
        $root->appendChild( $block );

        // Visit lines
        foreach ( $node->nodes as $child )
        {
            foreach ( $child->nodes as $literal )
            {
                $block->appendChild( new DOMText(
                    ( $literal->token->type !== ezcDocumentRstToken::NEWLINE ) ? $literal->token->content : ' '
                ) );
            }
            $break = $this->document->createElement( 'br' );
            $block->appendChild( $break );
        }
    }

    /**
     * Visit line block line
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitLineBlockLine( DOMNode $root, ezcDocumentRstNode $node )
    {
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }

        $break = $this->document->createElement( 'br' );
        $root->appendChild( $break );
    }

    /**
     * Visit comment
     *
     * @param DOMNode $root
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function visitComment( DOMNode $root, ezcDocumentRstNode $node )
    {
        $commentText = $this->nodeToString( $node );
        $comment = new DOMComment( $commentText );
        $root->appendChild( $comment );
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
        $term = $this->document->createElement( 'dt', htmlspecialchars( $this->tokenListToString( $node->name ) ) );
        $root->appendChild( $term );

        $definition = $this->document->createElement( 'dd' );
        $root->appendChild( $definition );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $definition, $child );
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
        $cell = $this->document->createElement( 'td' );
        $root->appendChild( $cell );

        if ( $node->rowspan > 1 )
        {
            $cell->setAttribute( 'rowspan', $node->rowspan );
        }

        if ( $node->colspan > 1 )
        {
            $cell->setAttribute( 'colspan', $node->colspan );
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
        $fieldName = strtolower( trim( $this->tokenListToString( $node->name ) ) );
        $meta = $this->document->createElement( 'meta' );
        $meta->setAttribute( 'name', htmlspecialchars( $fieldName ) );
        $meta->setAttribute( 'content', htmlspecialchars( trim( $this->nodeToString( $node ) ) ) );
        $this->head->appendChild( $meta );
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
        $directiveHandler->setSourceVisitor( $this );

        if ( !$directiveHandler instanceof ezcDocumentRstXhtmlDirective )
        {
            return $this->triggerError(
                E_WARNING, "Directive '{$handlerClass}' does not support HTML rendering.",
                null, $node->token->line, $node->token->position
            );
        }
        $directiveHandler->toXhtml( $this->document, $root );
    }
}

?>
