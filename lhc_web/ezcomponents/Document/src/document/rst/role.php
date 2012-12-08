<?php
/**
 * File containing the ezcDocumentRstTextRole class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST text roles
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentRstTextRole
{
    /**
     * Current text role RST AST node.
     *
     * @var ezcDocumentRstTextRoleNode
     */
    protected $node;

    /**
     * Complete RST abstract syntax tree, if this is necessary to render the
     * text role.
     *
     * @var ezcDocumentRstDocumentNode
     */
    protected $ast;

    /**
     * Current document base path, especially relevant for file inclusions.
     *
     * @var string
     */
    protected $path;

    /**
     * The calling visitor.
     *
     * @var ezcDocumentRstVisitor
     */
    protected $visitor;

    /**
     * Construct text role from AST and node
     *
     * @param ezcDocumentRstDocumentNode $ast
     * @param string $path
     * @param ezcDocumentRstMarkupInterpretedTextNode $node
     * @return void
     */
    public function __construct( ezcDocumentRstDocumentNode $ast, $path, ezcDocumentRstMarkupInterpretedTextNode $node )
    {
        $this->ast  = $ast;
        $this->path = $path;
        $this->node = $node;
    }

    /**
     * Set the calling vaisitor
     *
     * Pass the visitor which called the rendering function on the text role
     * for optional reference.
     *
     * @param ezcDocumentRstVisitor $visitor
     * @return void
     */
    public function setSourceVisitor( ezcDocumentRstVisitor $visitor )
    {
        $this->visitor = $visitor;
    }

    /**
     * Append text from interpreted text node to given DOMElement
     *
     * @param DOMElement $root
     * @return void
     */
    protected function appendText( DOMElement $root )
    {
        foreach ( $this->node->nodes as $node )
        {
            $root->appendChild( new DOMText( $node->token->content ) );
        }
    }

    /**
     * Transform text role to docbook
     *
     * Create a docbook XML structure at the text roles position in the
     * document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    abstract public function toDocbook( DOMDocument $document, DOMElement $root );
}

?>
