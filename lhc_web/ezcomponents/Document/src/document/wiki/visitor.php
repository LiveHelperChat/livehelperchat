<?php
/**
 * File containing the ezcDocumentWikiVisitor class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract visitor base for Wiki documents represented by the parser AST.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentWikiVisitor implements ezcDocumentErrorReporting
{
    /**
     * Wiki document handler
     *
     * @var ezcDocumentWiki
     */
    protected $wiki;

    /**
     * Reference to the AST root node.
     *
     * @var ezcDocumentWikiDocumentNode
     */
    protected $ast;

    /**
     * List with footnotes for later rendering.
     *
     * @var array
     */
    protected $footnotes = array();

    /**
     * Label dependant foot note counters for footnote auto enumeration.
     *
     * @var array
     */
    protected $footnoteCounter = 0;

    /**
     * Aggregated minor errors during document processing.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Create visitor from Wiki document handler.
     *
     * @param ezcDocumentWiki $document
     * @param string $path
     * @return void
     */
    public function __construct( ezcDocumentWiki $document, $path )
    {
        $this->wiki  = $document;
        $this->path = $path;
    }

    /**
     * Trigger visitor error
     *
     * Emit a vistitor error, and convert it to an exception depending on the
     * error reporting settings.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     * @return void
     */
    public function triggerError( $level, $message, $file = null, $line = null, $position = null )
    {
        if ( $level & $this->wiki->options->errorReporting )
        {
            throw new ezcDocumentVisitException( $level, $message, $file, $line, $position );
        }
        else
        {
            // If the error should not been reported, we aggregate it to maybe
            // display it later.
            $this->errors[] = new ezcDocumentVisitException( $level, $message, $file, $line, $position );
        }
    }

    /**
     * Return list of errors occured during visiting the document.
     *
     * May be an empty array, if on errors occured, or a list of
     * ezcDocumentVisitException objects.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Docarate Wiki AST
     *
     * Visit the Wiki abstract syntax tree.
     *
     * @param ezcDocumentWikiDocumentNode $ast
     * @return mixed
     */
    public function visit( ezcDocumentWikiDocumentNode $ast )
    {
        $this->ast = $ast;
        $this->preProcessAst( $ast );

        // Reset footnote counters
        $this->footnoteCounter = 0;
    }

    /**
     * Add footnote
     *
     * @param ezcDocumentWikiNode $node
     * @return void
     */
    protected function addFootnote( ezcDocumentWikiNode $node )
    {
        $number = ++$this->footnoteCounter;

        // Store footnote for later rendering in footnote array
        $node->number = $number;
        $this->footnotes[$number] = $node;
    }

    /**
     * Pre process AST
     *
     * Performs multiple preprocessing steps on the AST:
     *
     * Collect all possible reference targets in the AST to know the actual
     * destianation for references while decorating. The references are stored
     * in an internal structure and you may request the actual link by using
     * the getReferenceTarget() method.
     *
     * Aggregate list items into lists. In Wiki there are only list items, which
     * are aggregated to lists depending on their bullet type. The related list
     * items are aggregated into one list.
     *
     * @param ezcDocumentWikiNode $node
     * @return void
     */
    protected function preProcessAst( ezcDocumentWikiNode $node )
    {
        switch ( true )
        {
            case $node instanceof ezcDocumentWikiFootnoteNode:
                $this->addFootnote( $node );
                break;
        }

        // Recurse into childs to collect reference targets all over the
        // document.
        foreach ( $node->nodes as $child )
        {
            $this->preProcessAst( $child );
        }
    }

    /**
     * Check for internal footnote reference target
     *
     * Returns the target name, when an internal reference target exists and
     * sets it to used, and false otherwise.
     *
     * @param int $number
     * @param ezcDocumentWikiNode $node
     * @return ezcDocumentWikiFootnoteNode
     */
    public function hasFootnoteTarget( $number, ezcDocumentWikiNode $node )
    {
        if ( isset( $this->footnotes[$number] ) )
        {
            return $this->footnotes[$number];
        }

        return $this->triggerError(
            E_WARNING, "Unknown footnote reference target '{$number}'.", null,
            ( $node !== null ? $node->token->line : null ),
            ( $node !== null ? $node->token->position : null )
        );
    }

    /**
     * Transform a node tree into a string
     *
     * Transform a node tree, with all its subnodes into a string by only
     * getting the textuual contents from ezcDocumentWikiTextLineNode objects.
     *
     * @param ezcDocumentWikiNode $node
     * @return string
     */
    protected function nodeToString( ezcDocumentWikiNode $node )
    {
        $text = '';

        foreach ( $node->nodes as $child )
        {
            if ( ( $child instanceof ezcDocumentWikiTextLineNode ) ||
                 ( $child instanceof ezcDocumentWikiLiteralNode ) )
            {
                $text .= $child->token->content;
            }
            else
            {
                $text .= $this->nodeToString( $child );
            }
        }

        return $text;
    }

    /**
     * Node list to string
     *
     * Extract the contents of a node list and return a single string for the
     * array of nodes.
     *
     * @param array $nodes
     * @return string
     */
    protected function nodeListToString( array $nodes )
    {
        $text = '';

        foreach ( $nodes as $node )
        {
            $text .= $node->token->content;
        }

        return $text;
    }

    /**
     * Visit text node
     *
     * @param DOMNode $root
     * @param ezcDocumentWikiNode $node
     * @return void
     */
    protected function visitText( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $root->appendChild(
            new DOMText( preg_replace( '(\\s+)', ' ', $node->token->content ) )
        );
    }

    /**
     * Visit children
     *
     * Just recurse into node and visit its children, ignoring the actual
     * node.
     *
     * @param DOMNode $root
     * @param ezcDocumentWikiNode $node
     * @return void
     */
    protected function visitChildren( DOMNode $root, ezcDocumentWikiNode $node )
    {
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }
    }
}

?>
