<?php
/**
 * File containing the abstract ezcDocumentRstVisitor base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract visitor base for RST documents represented by the parser AST.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentRstVisitor implements ezcDocumentErrorReporting
{
    /**
     * RST document handler
     *
     * @var ezcDocumentRst
     */
    protected $rst;

    /**
     * Reference to the AST root node.
     *
     * @var ezcDocumentRstDocumentNode
     */
    protected $ast;

    /**
     * Location of the currently processed RST file, relevant for inclusion.
     *
     * @var string
     */
    protected $path;

    /**
     * Collected refrence targets.
     *
     * @var array
     */
    protected $references = array();

    /**
     * Counter of duplicate references for duplicate references.
     *
     * @var array
     */
    protected $referenceCounter = array();

    /**
     * Collected named external reference targets
     *
     * @var array
     */
    protected $namedExternalReferences = array();

    /**
     * Collected anonymous externals reference targets
     *
     * @var array
     */
    protected $anonymousReferences = array();

    /**
     * Index of last requested anonymous reference target.
     *
     * @var int
     */
    protected $anonymousReferenceCounter = 0;

    /**
     * Collected substitutions.
     *
     * @var array
     */
    protected $substitutions = array();

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
    protected $footnoteCounter = array( 0 );

    /**
     * Foot note symbol signs, as defined at
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#auto-symbol-footnotes
     *
     * @var array
     */
    protected $footnoteSymbols = array(
        "*",
        "\xE2\x80\xA0",
        "\xE2\x80\xA1",
        "\xC2\xA7",
        "\xC2\xB6",
        "#",
        "\xE2\x99\xA0",
        "\xE2\x99\xA5",
        "\xE2\x99\xA6",
        "\xE2\x99\xA3",
    );

    /**
     * Aggregated minor errors during document processing.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Array of already generated IDs, so none will be used twice.
     * 
     * @var array
     */
    protected $usedIDs = array();

    /**
     * Unused reference target
     */
    const UNUSED    = 1;

    /**
     * Used reference target
     */
    const USED      = 2;

    /**
     * Duplicate reference target. Will throw an error on use.
     */
    const DUBLICATE = 4;

    /**
     * Create visitor from RST document handler.
     *
     * @param ezcDocumentRst $document
     * @param string $path
     * @return void
     */
    public function __construct( ezcDocumentRst $document, $path )
    {
        $this->rst  = $document;
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
        if ( $level & $this->rst->options->errorReporting )
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
     * Docarate RST AST
     *
     * Visit the RST abstract syntax tree.
     *
     * @param ezcDocumentRstDocumentNode $ast
     * @return mixed
     */
    public function visit( ezcDocumentRstDocumentNode $ast )
    {
        $this->ast = $ast;
        $this->preProcessAst( $ast );

        // Reset footnote counters
        foreach ( $this->footnoteCounter as $label => $counter )
        {
            $this->footnoteCounter[$label] = 0;
        }
        reset( $this->footnoteSymbols );

        // Reset duplicate reference counter
        $this->referenceCounter = array();
    }

    /**
     * Add a reference target
     *
     * @param string $string
     * @return void
     */
    private function addReferenceTarget( $string )
    {
        $id = $this->calculateId( $string );
        $this->references[$id] = isset( $this->references[$id] ) ? self::DUBLICATE : self::UNUSED;

        if ( $this->references[$id] === self::UNUSED )
        {
            $this->referenceCounter[$id] = 0;
            return $id;
        }
        else
        {
            return $id . '__' . ( ++$this->referenceCounter[$id] );
        }
    }

    /**
     * Transform a node tree into a string
     *
     * Transform a node tree, with all its subnodes into a string by only
     * getting the textuual contents from ezcDocumentRstTextLineNode objects.
     *
     * @param ezcDocumentRstNode $node
     * @return string
     */
    public function nodeToString( ezcDocumentRstNode $node )
    {
        $text = '';

        foreach ( $node->nodes as $child )
        {
            if ( ( $child instanceof ezcDocumentRstTextLineNode ) ||
                 ( $child instanceof ezcDocumentRstLiteralNode ) )
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
     * Get string from token list.
     *
     * @param array $tokens
     * @return string
     */
    protected function tokenListToString( array $tokens )
    {
        $text = '';

        foreach ( $tokens as $token )
        {
            $text .= $token->content;
        }

        return $text;
    }

    /**
     * Compare two list items
     *
     * Check if the given list item may be a successor in the same list, as the
     * last item in the list. Returns the boolean status o the check.
     *
     * @param ezcDocumentRstNode $item
     * @param ezcDocumentRstNode $lastItem
     * @return bool
     */
    protected function compareListType( ezcDocumentRstNode $item, ezcDocumentRstNode $lastItem )
    {
        // Those always belong to each other... .oO( ♡ )
        if ( $item instanceof ezcDocumentRstDefinitionListNode )
        {
            return true;
        }

        // For bullet lists, just compare the tokens
        if ( $item instanceof ezcDocumentRstBulletListNode )
        {
            return ( $item->token->content === $lastItem->token->content );
        }

        // For enumerated lists, we need to check if the current value is a
        // valid successor of the prior value.
        if ( $item instanceof ezcDocumentRstEnumeratedListNode )
        {
            return ( $lastItem instanceof ezcDocumentRstEnumeratedListNode ) &&
                ( $item->listType === $lastItem->listType );
        }

        return true;
    }

    /**
     * Aggregate list items
     *
     * Aggregate list items into lists. In RST there are only list items, which
     * are aggregated to lists depending on their bullet type. The related list
     * items are aggregated into one list.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function aggregateListItems( ezcDocumentRstNode $node )
    {
        $listTypeMapping = array(
            'ezcDocumentRstBulletListNode'     => 'ezcDocumentRstBulletListListNode',
            'ezcDocumentRstEnumeratedListNode' => 'ezcDocumentRstEnumeratedListListNode',
            'ezcDocumentRstDefinitionListNode' => 'ezcDocumentRstDefinitionListListNode',
        );

        $lastItem = null;
        $list     = null;
        $children = array();
        foreach ( $node->nodes as $nr => $child )
        {
            if ( isset( $listTypeMapping[$class = get_class( $child )] ) )
            {
                if ( ( $lastItem === null ) ||
                     ( $list === null ) ||
                     ( !$this->compareListType( $child, $lastItem ) ) )
                {
                    // Create a new list.
                    $listType = $listTypeMapping[$class];
                    $list = new $listType( $child->token );
                    $list->nodes[] = $child;
                    $children[]    = $list;
                }
                else
                {
                    // Append to current list
                    $list->nodes[] = $child;
                }
                $lastItem = $child;
            }
            else
            {
                $children[] = $child;
                $lastItem = null;
            }
        }

        $node->nodes = $children;
    }

    /**
     * Add footnote
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function addFootnote( ezcDocumentRstNode $node )
    {
        $identifier = $this->tokenListToString( $node->name );

        switch ( $node->footnoteType )
        {
            case ezcDocumentRstFootnoteNode::NUMBERED:
                $label  = 0;
                $number = (int) $identifier;
                $this->footnoteCounter[0] = max( $number, $this->footnoteCounter[0] );
                break;
            case ezcDocumentRstFootnoteNode::AUTO_NUMBERED:
                $label  = 0;
                $number = !isset( $this->footnoteCounter[$label] ) ? ( $this->footnoteCounter[$label] = 1 ) : ++$this->footnoteCounter[$label];
                break;
            case ezcDocumentRstFootnoteNode::LABELED:
                $label  = substr( $identifier, 1 );
                $number = !isset( $this->footnoteCounter[$label] ) ? ( $this->footnoteCounter[$label] = 1 ) : ++$this->footnoteCounter[$label];
                break;
            case ezcDocumentRstFootnoteNode::SYMBOL:
                $label  = '*';
                $number = next( $this->footnoteSymbols );
                break;
            case ezcDocumentRstFootnoteNode::CITATION:
                $label  = '#';
                $number = $identifier;
                break;
        }

        // Store footnote for later rendering in footnote array
        $node->name   = $label;
        $node->number = $number;
        $this->footnotes[$label][$number] = $node;
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
     * Aggregate list items into lists. In RST there are only list items, which
     * are aggregated to lists depending on their bullet type. The related list
     * items are aggregated into one list.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function preProcessAst( ezcDocumentRstNode $node )
    {
        switch ( true )
        {
            case $node instanceof ezcDocumentRstDocumentNode:
                $this->aggregateListItems( $node );
                break;

            case $node instanceof ezcDocumentRstSectionNode:
                $node->reference = $this->addReferenceTarget( $this->nodeToString( $node->title ) );
                $this->aggregateListItems( $node );

                // Also recurse into special title subtree
                foreach ( $node->title->nodes as $child )
                {
                    $this->preProcessAst( $child );
                }
                break;

            case $node instanceof ezcDocumentRstTableCellNode:
            case $node instanceof ezcDocumentRstBulletListNode:
            case $node instanceof ezcDocumentRstEnumeratedListNode:
                $this->aggregateListItems( $node );
                break;

            case $node instanceof ezcDocumentRstTargetNode:
                $this->addReferenceTarget( $target = $this->nodeToString( $node ) );
                break;

            case $node instanceof ezcDocumentRstNamedReferenceNode:
                if ( count( $node->nodes ) )
                {
                    // This is a direct reference to an external URL, just add
                    // to the list of named external references.
                    $this->namedExternalReferences[$this->calculateId( $this->tokenListToString( $node->name ) )] =
                        trim( $this->nodeToString( $node ) );
                }
                break;

            case $node instanceof ezcDocumentRstAnonymousReferenceNode:
                $this->anonymousReferences[] = trim( $this->nodeToString( $node ) );
                break;

            case $node instanceof ezcDocumentRstSubstitutionNode:
                $substitutionName = strtolower( $this->tokenListToString( $node->name ) );
                $this->substitutions[$substitutionName] = $node->nodes;
                break;

            case $node instanceof ezcDocumentRstFootnoteNode:
                $this->addFootnote( $node );
                break;
        }

        // Check for forward references of empty named references.
        $children = $node->nodes;
        reset( $children );
        while ( $child = next( $children ) )
        {
            $stack = array();
            while ( ( $child instanceof ezcDocumentRstNamedReferenceNode ) &&
                    ( count( $child->nodes ) === 0 ) )
            {
                $stack[] = $child;
                $child = next( $children );
            }

            if ( $child && count( $stack ) )
            {
                // We found a element, which is not an empty named reference
                // node, so get the identifier from it and assign it to all
                // named references on the stack.
                if ( $child instanceof ezcDocumentRstNamedReferenceNode )
                {
                    // Child is a named reference with content, so use the
                    // content as assignement.
                    $reference = trim( $this->nodeToString( $child ) );
                }
                else
                {
                    // Generate a reference name and assign it to the element.
                    $last = end( $stack );
                    $reference = $this->calculateId( $this->tokenListToString( $last->name ) );
                    $child->identifier = $reference;
                }

                // Assign calculated reference to all aggregated stack
                // elements.
                foreach ( $stack as $refNode )
                {
                    $this->namedExternalReferences[$this->calculateId( $this->tokenListToString( $refNode->name ) )] = $reference;
                }
            }
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
     * @param string $string
     * @param ezcDocumentRstNode $node
     * @return ezcDocumentRstFootnoteNode
     */
    public function hasFootnoteTarget( $string, ezcDocumentRstNode $node )
    {
        switch ( $node->footnoteType )
        {
            case ezcDocumentRstFootnoteNode::NUMBERED:
                $label  = 0;
                $string = (int) $string;
                $this->footnoteCounter[0] = max( $string, $this->footnoteCounter[0] );
                break;
            case ezcDocumentRstFootnoteNode::AUTO_NUMBERED:
                $label  = 0;
                $string = ++$this->footnoteCounter[$label];
                break;
            case ezcDocumentRstFootnoteNode::LABELED:
                $label  = substr( $string, 1 );
                $string = ++$this->footnoteCounter[$label];
                break;
            case ezcDocumentRstFootnoteNode::SYMBOL:
                $label  = '*';
                $string = next( $this->footnoteSymbols );
                break;
            case ezcDocumentRstFootnoteNode::CITATION:
                $label  = '#';
                break;
        }

        if ( isset( $this->footnotes[$label][$string] ) )
        {
            return $this->footnotes[$label][$string];
        }

        return $this->triggerError(
            E_WARNING, "Unknown reference target '{$string}'.", null,
            ( $node !== null ? $node->token->line : null ),
            ( $node !== null ? $node->token->position : null )
        );
    }

    /**
     * Check for internal reference target
     *
     * Returns the target name, when an internal reference target exists and
     * sets it to used, and false otherwise. For duplicate reference targets
     * and missing reference targets an error will be triggered.
     *
     * An optional third parameter may enforce the fetching of the reference,
     * even if there are duplicates, so that they still can be referenced in
     * some way.
     *
     * @param string $string
     * @param ezcDocumentRstNode $node
     * @param bool $force
     * @return string
     */
    public function hasReferenceTarget( $string, ezcDocumentRstNode $node = null, $force = false )
    {
        $id = $this->calculateId( $string );
        if ( isset( $this->references[$id] ) &&
             ( $this->references[$id] !== self::DUBLICATE ) )
        {
            $this->references[$id] = self::USED;
            return $id;
        }

        if ( !isset( $this->references[$id] ) )
        {
            return $this->triggerError(
                E_WARNING, "Missing reference target '{$id}'.", null,
                ( $node !== null ? $node->token->line : null ),
                ( $node !== null ? $node->token->position : null )
            );
        }
        elseif ( $force === true )
        {
            // Check if the reference target has been force-requested.
            if ( !isset( $this->referenceCounter[$id] ) )
            {
                $this->referenceCounter[$id] = 0;
                return $id;
            }
            else
            {
                return $id . '__' . ( ++$this->referenceCounter[$id] );
            }
        }
        else
        {
            return $this->triggerError(
                E_NOTICE, "Duplicate reference target '{$id}'.", null,
                ( $node !== null ? $node->token->line : null ),
                ( $node !== null ? $node->token->position : null )
            );
        }
    }

    /**
     * Return named external reference target
     *
     * Get the target value of a named external reference.
     *
     * @param string $name
     * @return string
     */
    public function getNamedExternalReference( $name )
    {
        $name = $this->calculateId( $name );

        if ( isset( $this->namedExternalReferences[$name] ) )
        {
            return $this->namedExternalReferences[$name];
        }

        return false;
    }

    /**
     * Get anonymous reference target
     *
     * Get the target URL of an anonomyous reference target.
     *
     * @return string
     */
    public function getAnonymousReferenceTarget()
    {
        if ( isset( $this->anonymousReferences[$this->anonymousReferenceCounter] ) )
        {
            return $this->anonymousReferences[$this->anonymousReferenceCounter++];
        }

        return $this->triggerError(
            E_WARNING, "Too few anonymous reference targets.", null
        );
    }

    /**
     * Get substitution contents
     *
     * @param string $string
     * @return void
     */
    protected function substitute( $string )
    {
        $string = strtolower( $string );
        if ( isset( $this->substitutions[$string] ) )
        {
            return $this->substitutions[$string];
        }

        $this->triggerError(
            E_ERROR, "Could not find substitution for '{$string}'.", null
        );
        return array();
    }

    /**
     * Get a valid identifier string
     *
     * Get a valid identifier string from an arbritrary string.
     *
     * @param string $string
     * @return string
     */
    protected function calculateId( $string )
    {
        $id = trim( preg_replace( '([^a-z0-9-]+)', '_', strtolower( trim( $string ) ) ), '_' );
        if ( !preg_match( '(^[a-z])', $id ) )
        {
            $id = 'id_' . $id;
        }

        return $id;
    }

    /**
     * Calculate unique ID
     *
     * Calculate a valid identifier, which is unique for this document.
     * 
     * @param string $string 
     * @return string
     */
    protected function calculateUniqueId( $string )
    {
        $id = $this->calculateId( $string );

        // Ensure uniqueness of IDs
        if ( isset( $this->usedIDs[$id] ) )
        {
            $i = 2;
            do {
                $tryId = $id . '_' . $i++;
            } while ( isset( $this->usedIDs[$tryId] ) );
            $id = $tryId;
        }
        $this->usedIDs[$id] = true;

        return $id;
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
}

?>
