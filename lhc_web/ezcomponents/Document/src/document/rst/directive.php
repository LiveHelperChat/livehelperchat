<?php
/**
 * File containing the ezcDocumentRstDirective class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST directives
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentRstDirective
{
    /**
     * Current directive RST AST node.
     *
     * @var ezcDocumentRstDirectiveNode
     */
    protected $node;

    /**
     * Complete RST abstract syntax tree, if this is necessary to render the
     * directive.
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
     * Construct directive from AST and node
     *
     * @param ezcDocumentRstDocumentNode $ast
     * @param string $path
     * @param ezcDocumentRstDirectiveNode $node
     * @return void
     */
    public function __construct( ezcDocumentRstDocumentNode $ast, $path, ezcDocumentRstDirectiveNode $node )
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
     * @param ezcDocumentRstVisitor $visitor
     * @return void
     */
    public function setSourceVisitor( ezcDocumentRstVisitor $visitor )
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

    /**
     * Parse directive token list with RST parser
     *
     * This method is intended to parse the token list, provided for the RST 
     * contents using the standard RST parser. It will be visited afterwards by 
     * the provided RST-visitor implementation.
     *
     * The method returns the created document as a DOMDocument. You normally 
     * need to use DOMDocument::importNode to embed the conatined nodes in your 
     * target document.
     * 
     * @param array $tokens 
     * @param ezcDocumentRstVisitor $visitor 
     * @return DOMDocument
     */
    protected function parseTokens( array $tokens, ezcDocumentRstVisitor $visitor )
    {
        $parser = new ezcDocumentRstParser();
        $ast = $parser->parse( $tokens );

        $doc = $visitor->visit( $ast, $this->path );
        return $doc;
    }
}

?>
