<?php
/**
 * File containing the ezcDocumentWikiPlugin class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for wiki directives
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentWikiPlugin
{
    /**
     * Current directive wiki AST node.
     *
     * @var ezcDocumentWikiPluginNode
     */
    protected $node;

    /**
     * Complete wiki abstract syntax tree, if this is necessary to render the
     * directive.
     *
     * @var ezcDocumentWikiDocumentNode
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
     * @var ezcDocumentWikiVisitor
     */
    protected $visitor;

    /**
     * Construct directive from AST and node
     *
     * @param ezcDocumentWikiDocumentNode $ast
     * @param string $path
     * @param ezcDocumentWikiPluginNode $node
     * @return void
     */
    public function __construct( ezcDocumentWikiDocumentNode $ast, $path, ezcDocumentWikiPluginNode $node )
    {
        $this->ast  = $ast;
        $this->path = $path;
        $this->node = $node;
    }

    /**
     * Set the calling vaisitor
     *
     * Pass the visitor which called the rendering function on the directive
     * for optional reference.
     *
     * @param ezcDocumentWikiVisitor $visitor
     * @return void
     */
    public function setSourceVisitor( ezcDocumentWikiVisitor $visitor )
    {
        $this->visitor = $visitor;
    }

    /**
     * Transform directive to docbook
     *
     * Create a docbook XML structure at the directives position in the
     * document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    abstract public function toDocbook( DOMDocument $document, DOMElement $root );
}

?>
