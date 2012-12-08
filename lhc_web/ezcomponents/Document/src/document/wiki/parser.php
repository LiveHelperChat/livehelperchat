<?php
/**
 * File containing the ezcDocumentWikiParser class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parser for wiki documents.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiParser extends ezcDocumentParser
{
    /**
     * Array containing simplified shift ruleset.
     *
     * We cannot express the Wiki syntax as a usual grammar using a BNF. With
     * the pumping lemma for context free grammars [1] you can easily prove,
     * that the word a^n b c^n d e^n is not a context free grammar, and this is
     * what the title definitions are.
     *
     * This structure contains an array with callbacks implementing the shift
     * rules for all tokens. There may be multiple rules for one single token.
     *
     * The callbacks itself create syntax elements and push them to the
     * document stack. After each push the reduction callbacks will be called
     * for the pushed elements.
     *
     * The array should look like:
     * <code>
     *  array(
     *      WHITESPACE => array(
     *          reductionMethod,
     *          ...
     *      ),
     *      ...
     *  )
     * </code>
     *
     * [1] http://en.wikipedia.org/wiki/Pumping_lemma_for_context-free_languages
     *
     * @var array
     */
    protected $shifts = array(
        'ezcDocumentWikiEscapeCharacterToken'
            => 'shiftEscapeToken',
        'ezcDocumentWikiTitleToken'
            => 'shiftTitleToken',
        'ezcDocumentWikiNewLineToken'
            => 'shiftNewLineToken',
        'ezcDocumentWikiEscapeCharacterToken'
            => 'shiftEscapeToken',
        'ezcDocumentWikiToken'
            => 'shiftWithTokenConversion',
    );

    /**
     * Array containing simplified reduce ruleset.
     *
     * We cannot express the Wiki syntax as a usual grammar using a BNF. This
     * structure implements a pseudo grammar by assigning a number of callbacks
     * for internal methods implementing reduction rules for a detected syntax
     * element.
     *
     * <code>
     *  array(
     *      ezcDocumentWikiNode::DOCUMENT => 'reduceDocument'
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $reductions = array(
        'ezcDocumentWikiTextNode' => array(
            'reduceText',
        ),
        'ezcDocumentWikiParagraphNode' => array(
            'reduceParagraph',
        ),
        'ezcDocumentWikiInvisibleBreakNode' => array(
            'reduceLineNode',
        ),
        'ezcDocumentWikiTitleNode' => array(
            'reduceTitleToSection',
        ),
        'ezcDocumentWikiSectionNode' => array(
            'reduceLists',
            'reduceSection',
        ),
        'ezcDocumentWikiMatchingInlineNode' => array(
            'reduceMatchingInlineMarkup',
        ),
        'ezcDocumentWikiBlockquoteNode' => array(
            'reduceBlockquoteNode',
        ),
        'ezcDocumentWikiLinkEndNode' => array(
            'reduceLinkNodes',
        ),
        'ezcDocumentWikiImageEndNode' => array(
            'reduceImageNodes',
        ),
        'ezcDocumentWikiFootnoteEndNode' => array(
            'reduceFootnoteNodes',
        ),
        'ezcDocumentWikiBulletListItemNode' => array(
            'reduceBulletListItem',
        ),
        'ezcDocumentWikiEnumeratedListItemNode' => array(
            'reduceEnumeratedListItem',
        ),
        'ezcDocumentWikiTableRowNode' => array(
            'reduceTableRow',
        ),
    );

    /**
     * Contains a list of detected syntax elements.
     *
     * At the end of a successfull parsing process this should only contain one
     * document syntax element. During the process it may contain a list of
     * elements, which are up to reduction.
     *
     * Each element in the stack has to be an object extending from
     * ezcDocumentRstNode, which may again contain any amount such objects.
     * This way an abstract syntax tree is constructed.
     *
     * @var array
     */
    protected $documentStack = array();

    /**
     * Flag if we are inside a line level node
     *
     * @var bool
     */
    protected $insideLineToken = false;

    /**
     * Array with token node conversions.
     *
     * Token to node conversions are used for tokens, which do not require any
     * additional checking of the tokens context. This is especially useful,
     * because the wiki tokenizer already implement a lot of this logic.
     *
     * @var array
     */
    protected $conversionsArray = array(
        'ezcDocumentWikiEndOfFileToken'            => 'ezcDocumentWikiDocumentNode',
        'ezcDocumentWikiTextLineToken'             => 'ezcDocumentWikiTextNode',
        'ezcDocumentWikiWhitespaceToken'           => 'ezcDocumentWikiTextNode',
        'ezcDocumentWikiSpecialCharsToken'         => 'ezcDocumentWikiTextNode',

        'ezcDocumentWikiTitleToken'                => 'ezcDocumentWikiTitleNode',
        'ezcDocumentWikiParagraphIndentationToken' => 'ezcDocumentWikiBlockquoteNode',
        'ezcDocumentWikiQuoteToken'                => 'ezcDocumentWikiBlockquoteNode',
        'ezcDocumentWikiPageBreakToken'            => 'ezcDocumentWikiPageBreakNode',
        'ezcDocumentWikiBulletListItemToken'       => 'ezcDocumentWikiBulletListItemNode',
        'ezcDocumentWikiEnumeratedListItemToken'   => 'ezcDocumentWikiEnumeratedListItemNode',
        'ezcDocumentWikiLiteralBlockToken'         => 'ezcDocumentWikiLiteralBlockNode',
        'ezcDocumentWikiTableRowToken'             => 'ezcDocumentWikiTableRowNode',
        'ezcDocumentWikiPluginToken'               => 'ezcDocumentWikiPluginNode',

        'ezcDocumentWikiBoldToken'                 => 'ezcDocumentWikiBoldNode',
        'ezcDocumentWikiItalicToken'               => 'ezcDocumentWikiItalicNode',
        'ezcDocumentWikiUnderlineToken'            => 'ezcDocumentWikiUnderlineNode',
        'ezcDocumentWikiMonospaceToken'            => 'ezcDocumentWikiMonospaceNode',
        'ezcDocumentWikiSubscriptToken'            => 'ezcDocumentWikiSubscriptNode',
        'ezcDocumentWikiSuperscriptToken'          => 'ezcDocumentWikiSuperscriptNode',
        'ezcDocumentWikiDeletedToken'              => 'ezcDocumentWikiDeletedNode',
        'ezcDocumentWikiStrikeToken'               => 'ezcDocumentWikiDeletedNode',
        'ezcDocumentWikiInlineQuoteToken'          => 'ezcDocumentWikiInlineQuoteNode',
        'ezcDocumentWikiLineBreakToken'            => 'ezcDocumentWikiLineBreakNode',
        'ezcDocumentWikiInlineLiteralToken'        => 'ezcDocumentWikiInlineLiteralNode',

        'ezcDocumentWikiSeparatorToken'            => 'ezcDocumentWikiSeparatorNode',
        'ezcDocumentWikiTableHeaderToken'          => 'ezcDocumentWikiTableHeaderSeparatorNode',

        'ezcDocumentWikiExternalLinkToken'         => 'ezcDocumentWikiExternalLinkNode',
        'ezcDocumentWikiInterWikiLinkToken'        => 'ezcDocumentWikiInterWikiLinkNode',
        'ezcDocumentWikiInternalLinkToken'         => 'ezcDocumentWikiInternalLinkNode',
        'ezcDocumentWikiLinkStartToken'            => 'ezcDocumentWikiLinkNode',
        'ezcDocumentWikiLinkEndToken'              => 'ezcDocumentWikiLinkEndNode',

        'ezcDocumentWikiImageStartToken'           => 'ezcDocumentWikiImageNode',
        'ezcDocumentWikiImageEndToken'             => 'ezcDocumentWikiImageEndNode',

        'ezcDocumentWikiFootnoteStartToken'        => 'ezcDocumentWikiFootnoteNode',
        'ezcDocumentWikiFootnoteEndToken'          => 'ezcDocumentWikiFootnoteEndNode',
    );

    /**
     * Parse token stream.
     *
     * Parse an array of ezcDocumentWikiToken objects into a wiki abstract
     * syntax tree.
     *
     * @param array $tokens
     * @return ezcDocumentWikiDocumentNode
     */
    public function parse( array $tokens )
    {
        /* DEBUG
        echo "\n\nStart parser\n============\n\n";
        // /DEBUG */

        while ( ( $token = array_shift( $tokens ) ) !== null )
        {
            /* DEBUG
            echo "[T] ({$token->line}:{$token->position}) Token: " . get_class( $token ) . " at {$token->line}:{$token->position}.\n";
            // /DEBUG */

            // First shift given token by the defined reduction methods
            $node = false;
            foreach ( $this->shifts as $class => $method )
            {
                if ( $token instanceof $class )
                {
                    /* DEBUG
                    echo " - Handle token with ->$method\n";
                    // /DEBUG */

                    // Try to shift the token with current method
                    if ( ( $node = $this->$method( $token, $tokens ) ) !== false )
                    {
                        break;
                    }
                }
            }

            // If the node is still null there was not matching shift rule.
            if ( $node === false )
            {
                return $this->triggerError( E_PARSE,
                    "Could not find shift rule for token '" . get_class( $token ) . "'.",
                    $token->line, $token->position
                );
            }

            // Token did not result in any node, it should just be ignored.
            if ( $node === null )
            {
                continue;
            }

            /* DEBUG
            echo "[N] Node: " . get_class( $node ) . " at {$node->token->line}:{$node->token->position}.\n";
            // /DEBUG */

            // Apply reductions to shifted node
            do {
                foreach ( $this->reductions as $class => $methods )
                {
                    if ( $node instanceof $class )
                    {
                        foreach ( $methods as $method )
                        {
                            /* DEBUG
                            echo " - Handle node with ->$method\n";
                            // /DEBUG */

                            if ( ( $node = $this->$method( $node ) ) === null )
                            {
                                /* DEBUG
                                echo "   - Reduced.\n";
                                // /DEBUG */
                                // The node has been handled, exit loop.
                                break 3;
                            }

                            // Check if the node class has changed and rehandle
                            // node in this case.
                            if ( !$node instanceof $class )
                            {
                                /* DEBUG
                                echo "   - Try subsequent reductions...\n";
                                // /DEBUG */

                                continue 2;
                            }
                        }
                    }
                }
            } while ( false );

            // Check if reductions have been applied, but still returned a
            // node, just add to document stack in this case.
            if ( $node !== null )
            {
                /* DEBUG
                echo " => Prepend " . get_class( $node ) . " to document stack.\n";
                // /DEBUG */
                array_unshift( $this->documentStack, $node );
            }
        }

        // Check if we successfully reduced the document stack
        if ( ( count( $this->documentStack ) !== 1 ) ||
             ( !( $document = reset( $this->documentStack ) ) instanceof ezcDocumentWikiDocumentNode ) )
        {
            $node = isset( $document ) ? $document : reset( $this->documentStack );
            $this->triggerError(
                E_PARSE,
                'Expected end of file, got: ' . get_class( $this->documentStack[1] ) . ".",
                $this->documentStack[1]->token->line, $this->documentStack[1]->token->position
            );
        }

        return $document;
    }

    /**
     * Shift escape token.
     *
     * Escape tokens will cause that the following token is ignored in his
     * common meaning. The following token is converted to plain text, while
     * the escape token will be removed.
     *
     * @param ezcDocumentWikiToken $token
     * @param array $tokens
     * @return mixed
     */
    protected function shiftEscapeToken( ezcDocumentWikiToken $token, array &$tokens )
    {
        // If there is nothing to escape, shift as text node
        if ( !isset( $tokens[0] ) ||
             ( $tokens[0] instanceof ezcDocumentWikiWhitespaceToken ) ||
             ( $tokens[0] instanceof ezcDocumentWikiNewLineToken ) )
        {
            return new ezcDocumentWikiTextNode( $token );
        }

        // Otherwise shift the following token as text node
        $token = array_shift( $tokens );
        return new ezcDocumentWikiTextNode( $token );
    }

    /**
     * Shift title token.
     *
     * Some wiki markup languages use a second title token at the end of the
     * line instead of just a line break. In the case we are already inside a
     * line token, just shift an invisible line break.
     *
     * @param ezcDocumentWikiToken $token
     * @param array $tokens
     * @return mixed
     */
    protected function shiftTitleToken( ezcDocumentWikiToken $token, array &$tokens )
    {
        if ( $this->insideLineToken )
        {
            // If the title token is already the one in the next line reprepend
            // it to the token list.
            if ( $token->position === 0 )
            {
                array_unshift( $tokens, $token );
            }

            $this->insideLineToken = false;
            return new ezcDocumentWikiInvisibleBreakNode( $token );
        }

        return false;
    }

    /**
     * Shift new line token.
     *
     * Paragraphs are always indicated by multiple new line tokens. When
     * detected we just shift a paragraph node, which the will be reduced with
     * prior inline nodes.
     *
     * @param ezcDocumentWikiToken $token
     * @param array $tokens
     * @return mixed
     */
    protected function shiftNewLineToken( ezcDocumentWikiToken $token, array &$tokens )
    {
        // Wiki markup knows a lot of markup, which is limited to one line. If
        // a token starting su a line the $insideLineToken flag is set true and
        // we shift an end marker to the stack for a single new line.
        if ( $this->insideLineToken )
        {
            /* DEBUG
            echo "  -> End of line markup.\n";
            // /DEBUG */

            $this->insideLineToken = false;
            return new ezcDocumentWikiInvisibleBreakNode( $token );
        }

        // Shift a single newline as a paragrapoh, if the following token is a
        // block level token.
        if ( isset( $tokens[0] ) &&
             ( $tokens[0] instanceof ezcDocumentWikiBlockMarkupToken ) &&
             isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) )
        {
            /* DEBUG
            echo "  -> End of paragraph, because of block level token.\n";
            // /DEBUG */

            return new ezcDocumentWikiParagraphNode( $token );
        }

        // Only shift a paragraph node, if there are multiple new lines, and if
        // there is already inline markup on the document stack.
        if ( isset( $tokens[0] ) &&
             ( $tokens[0] instanceof ezcDocumentWikiNewLineToken ) &&
             isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) )
        {
            // Remove all subsequent new line tokens.
            do {
                array_shift( $tokens );
            } while ( isset( $tokens[0] ) &&
                      ( $tokens[0] instanceof ezcDocumentWikiNewLineToken ) );

            /* DEBUG
            echo "  -> End of paragraph, because of multiple newlines.\n";
            // /DEBUG */

            return new ezcDocumentWikiParagraphNode( $token );
        }

        // Return all other newlines as text nodes - they may be whitespaces
        // required in text, if we are inside a paragraph node
        if ( isset( $this->documentStack[0] ) &&
             ( ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) ||
               ( $this->documentStack[0] instanceof ezcDocumentWikiParagraphNode ) ) )
        {
            return new ezcDocumentWikiTextNode( $token );
        }

        // Otherwise just ommit newline
        return null;
    }

    /**
     * Shift with token conversion.
     *
     * Token to node conversions are used for tokens, which do not require any
     * additional checking of the tokens context. This is especially useful,
     * because the wiki tokenizer already implement a lot of this logic.
     *
     * The actual conversions are specified in the class property
     * $conversionsArray.
     *
     * @param ezcDocumentWikiToken $token
     * @param array $tokens
     * @return mixed
     */
    protected function shiftWithTokenConversion( ezcDocumentWikiToken $token, array &$tokens )
    {
        foreach ( $this->conversionsArray as $tokenClass => $nodeClass )
        {
            if ( $token instanceof $tokenClass )
            {
                if ( $token instanceof ezcDocumentWikiLineMarkupToken )
                {
                    $this->insideLineToken = true;
                }

                /* DEBUG
                echo "  -> Converted  to $nodeClass (" . ( (int) $this->insideLineToken ) . ")\n";
                // /DEBUG */
                return new $nodeClass( $token );
            }
        }

        return false;
    }

    /**
     * Reduce text nodes.
     *
     * Reduce texts into single nodes, if the prior node is also a text node.
     * This reduces the number of AST nodes required to represent texts
     * drastically.
     *
     * @param ezcDocumentWikiTextNode $node
     * @return mixed
     */
    protected function reduceText( ezcDocumentWikiTextNode $node )
    {
        if ( isset( $this->documentStack[0] ) &&
            ( $this->documentStack[0] instanceof ezcDocumentWikiTextNode ) )
        {
            $this->documentStack[0]->token->content .= $node->token->content;
            return null;
        }

        return $node;
    }

    /**
     * Reduce paragraph.
     *
     * Paragraphs are reduce with all inline tokens, which have been added to
     * the document stack before. If there are no inline nodes, the paragraph
     * will be ommitted.
     *
     * @param ezcDocumentWikiParagraphNode $node
     * @return mixed
     */
    protected function reduceParagraph( ezcDocumentWikiParagraphNode $node )
    {
        // Collect inline nodes
        $collected = array();
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) )
        {
            $inlineNode = array_shift( $this->documentStack );

            // Convert markup nodes without matching equivalent or out of the
            // normal context to text nodes.
            if ( ( ( $inlineNode instanceof ezcDocumentWikiMatchingInlineNode ) &&
                   ( $inlineNode->nodes === array() ) ) ||
                 ( ( $inlineNode instanceof ezcDocumentWikiSeparatorNode ) ) ||
                 ( ( $inlineNode instanceof ezcDocumentWikiImageNode ) &&
                   ( $inlineNode->resource === array() ) ) ||
                 ( ( $inlineNode instanceof ezcDocumentWikiFootnoteNode ) &&
                   ( $inlineNode->nodes === array() ) ) ||
                 ( ( $inlineNode instanceof ezcDocumentWikiLinkNode ) &&
                   ( $inlineNode->link === array() ) ) )
            {
                $inlineNode = new ezcDocumentWikiTextNode( $inlineNode->token );
            }

            array_unshift( $collected, $inlineNode );
        }

        if ( !count( $collected ) )
        {
            // No tokens found, we can ommit the paragraph.
            return null;
        }
        $node->nodes = array_values( $collected );

        if ( isset( $this->documentStack[0] ) &&
             ( ( $this->documentStack[0] instanceof ezcDocumentWikiBulletListItemNode ) ||
               ( $this->documentStack[0] instanceof ezcDocumentWikiEnumeratedListItemNode ) ) &&
             ( $this->documentStack[0]->nodes === array() ) )
        {
            $paragraph = $node;
            $node = array_shift( $this->documentStack );
            $node->nodes[] = $paragraph;
        }

        return $node;
    }

    /**
     * Reduce prior sections, if a new section has been found.
     *
     * If a new section has been found all sections with a higher depth level
     * can be closed, and all items fitting into sections may be aggregated by
     * the respective sections as well.
     *
     * @param ezcDocumentWikiSectionNode $node
     */
    protected function reduceSection( ezcDocumentWikiSectionNode $node )
    {
        // Collected node for prior section
        $collected = array();
        $lastSectionLevel = -1;

        // Include all paragraphs, tables, lists and sections with a higher
        // nesting depth
        while ( $child = array_shift( $this->documentStack ) )
        {
            /* DEBUG
            echo "  -> Try node: " . get_class( $child ) . ".\n";
            // /DEBUG */
            if ( !$child instanceof ezcDocumentWikiBlockLevelNode )
            {
                $this->triggerError(
                    E_PARSE,
                    "Unexpected node: " . get_class( $child ) . ".",
                    null, $child->token->line, $child->token->position
                );
            }

            if ( $child instanceof ezcDocumentWikiSectionNode )
            {
                if ( $child->level <= $node->level )
                {
                    $child->nodes = array_merge(
                        $child->nodes,
                        $collected
                    );
                    // If the found section has a same or higher level, just
                    // put it back on the stack
                    array_unshift( $this->documentStack, $child );
                    /* DEBUG
                    echo "   -> Leave on stack.\n";
                    // /DEBUG */
                    return $node;
                }

                if ( ( $lastSectionLevel - $child->level ) > 1 )
                {
                    $this->triggerError(
                        E_NOTICE,
                        "Title depth inconsitency.",
                        null, $child->token->line, $child->token->position
                    );
                }

                if ( ( $lastSectionLevel === -1 ) ||
                     ( $lastSectionLevel > $child->level ) )
                {
                    // If the section level is higher then in our new node and
                    // lower the the last node, reduce sections.
                    /* DEBUG
                    echo "   -> Reduce section {$child->level}.\n";
                    // /DEBUG */
                    $child->nodes = array_merge(
                        $child->nodes,
                        $collected
                    );
                    $collected = array();
                }

                // Sections on an equal level are just appended, for all
                // sections we remember the last depth.
                $lastSectionLevel = $child->level;
            }

            array_unshift( $collected, $child );
        }

        $node->nodes = array_merge(
            $node->nodes,
            $collected
        );
        return $node;
    }

    /**
     * Reduce all elements to one document node.
     *
     * @param ezcDocumentWikiTitleNode $node
     */
    protected function reduceTitleToSection( ezcDocumentWikiTitleNode $node )
    {
        if ( $node->nodes === array() )
        {
            // Title node has no content yet, skip for now.
            return $node;
        }

        // Prepend section element to document stack
        $section = new ezcDocumentWikiSectionNode( $node->token );
        $section->nodes = array( $node );
        return $section;
    }

    /**
     * Reduce matching inline markup.
     *
     * Reduction rule for inline markup which is intended to have a matching
     * counterpart in the same block level element.
     *
     * @param ezcDocumentWikiMatchingInlineNode $node
     * @return mixed
     */
    protected function reduceMatchingInlineMarkup( ezcDocumentWikiMatchingInlineNode $node )
    {
        // Collect inline nodes
        $collected = array();
        $class     = get_class( $node );
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) &&
                ( ( !$this->documentStack[0] instanceof $class ) ||
                  ( $this->documentStack[0]->nodes !== array() ) ) )
        {
            array_unshift( $collected, array_shift( $this->documentStack ) );
        }

        if ( isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0] instanceof $class ) &&
             ( $this->documentStack[0]->nodes === array() ) )
        {
            // We found an empty matching node. Reduce
            $markupNode = array_shift( $this->documentStack );
            $markupNode->nodes = $collected;
            return $markupNode;
        }
        else
        {
            // No matching node found, just leave it on the stack.
            $this->documentStack = array_merge( array_reverse( $collected ), $this->documentStack );
            return $node;
        }
    }

    /**
     * Reduce line node.
     *
     * Line nodes are closed at the end of their respective line. The end is
     * marked by an ezcDocumentWikiInvisibleBreakNode.
     *
     * @param ezcDocumentWikiInvisibleBreakNode $node
     * @return mixed
     */
    protected function reduceLineNode( ezcDocumentWikiInvisibleBreakNode $node )
    {
        // Collect inline nodes
        /* DEBUG
        echo "  -> Find childs for line level markup:";
        // /DEBUG */
        $collected = array();
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) )
        {
            array_unshift( $collected, array_shift( $this->documentStack ) );
            /* DEBUG
            echo ".";
            // /DEBUG */
        }
        /* DEBUG
        echo " " . count( $collected ) . " found.\n";
        // /DEBUG */

        if ( count( $collected ) &&
             isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0] instanceof ezcDocumentWikiLineLevelNode ) )
        {
            $lineNode = array_shift( $this->documentStack );
            $lineNode->nodes = $collected;

            /* DEBUG
            echo "  => Reduce line node: " . get_class( $lineNode ) . "\n";
            // /DEBUG */
            return $lineNode;
        }

        // No tokens found, we can ommit the break node, and put everything
        // back on the document stack
        /* DEBUG
        echo "  => Nothing found (", get_class( $this->documentStack[0] ), ") - line break ommitted.\n";
        // /DEBUG */
        $this->documentStack = array_merge( array_reverse( $collected ), $this->documentStack );
        return null;
    }

    /**
     * Reduce wiki links.
     *
     * Reduce links with all of their aggregated parameters.
     *
     * @param ezcDocumentWikiLinkEndNode $node
     * @return mixed
     */
    protected function reduceLinkNodes( ezcDocumentWikiLinkEndNode $node )
    {
        // Collect inline nodes
        $parameters = array( array() );
        $collected  = array();
        $parameter  = 0;
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) &&
                ( !$this->documentStack[0] instanceof ezcDocumentWikiLinkNode ) )
        {
            $child = array_shift( $this->documentStack );
            $collected[] = $child;

            if ( $child instanceof ezcDocumentWikiSeparatorNode )
            {
                $parameters[++$parameter] = array();
            }
            else
            {
                array_unshift( $parameters[$parameter], $child );
            }
        }

        // We could not find a corresponding start element, put everything back
        // on stack and convert node into plain text
        if ( !isset( $this->documentStack[0] ) ||
             ( !$this->documentStack[0] instanceof ezcDocumentWikiLinkNode ) )
        {
            $this->documentStack = array_merge( $collected, $this->documentStack );
            return new ezcDocumentWikiTextNode( $node->token );
        }

        // Reverse parameter order
        $parameters = array_reverse( $parameters );
        $linkStart = array_shift( $this->documentStack );

        $parameter = $linkStart->token->getLinkParameterOrder( count( $parameters ) );
        foreach ( $parameter as $nr => $name )
        {
            $linkStart->$name = $parameters[$nr];
        }

        return $linkStart;
    }

    /**
     * Reduce wiki image references.
     *
     * Reduce image references with all of their aggregated parameters.
     *
     * @param ezcDocumentWikiImageEndNode $node
     * @return mixed
     */
    protected function reduceImageNodes( ezcDocumentWikiImageEndNode $node )
    {
        // Collect inline nodes
        $parameters = array( array() );
        $collected  = array();
        $parameter  = 0;
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) &&
                ( !$this->documentStack[0] instanceof ezcDocumentWikiImageNode ) )
        {
            $child = array_shift( $this->documentStack );
            $collected[] = $child;

            if ( $child instanceof ezcDocumentWikiSeparatorNode )
            {
                $parameters[++$parameter] = array();
            }
            else
            {
                array_unshift( $parameters[$parameter], $child );
            }
        }

        // We could not find a corresponding start element, put everything back
        // on stack and convert node into plain text
        if ( !isset( $this->documentStack[0] ) ||
             ( !$this->documentStack[0] instanceof ezcDocumentWikiImageNode ) )
        {
            $this->documentStack = array_merge( $collected, $this->documentStack );
            return new ezcDocumentWikiTextNode( $node->token );
        }

        // Reverse parameter order
        $parameters = array_reverse( $parameters );
        $linkStart = array_shift( $this->documentStack );

        // Apply token parameters, which may be overwritten by parameters
        // detected from parser
        $linkStart->alignement = $linkStart->token->alignement;
        $linkStart->width      = $linkStart->token->width;
        $linkStart->height     = $linkStart->token->height;

        $parameter = $linkStart->token->getImageParameterOrder( count( $parameters ) );
        foreach ( $parameter as $nr => $name )
        {
            $linkStart->$name = $parameters[$nr];
        }

        return $linkStart;
    }

    /**
     * Reduce wiki footnotes.
     *
     * Reduce inline footnotes
     *
     * @param ezcDocumentWikiFootnoteEndNode $node
     * @return mixed
     */
    protected function reduceFootnoteNodes( ezcDocumentWikiFootnoteEndNode $node )
    {
        // Collect inline nodes
        $collected = array();
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) &&
                ( !$this->documentStack[0] instanceof ezcDocumentWikiFootnoteNode ) )
        {
            array_unshift( $collected, array_shift( $this->documentStack ) );
        }

        // We could not find a corresponding start element, put everything back
        // on stack and convert node into plain text
        if ( !isset( $this->documentStack[0] ) ||
             ( !$this->documentStack[0] instanceof ezcDocumentWikiFootnoteNode ) )
        {
            $this->documentStack = array_merge( array_reverse( $collected ), $this->documentStack );
            return new ezcDocumentWikiTextNode( $node->token );
        }

        // Reverse parameter order
        $footnote        = array_shift( $this->documentStack );
        $footnote->nodes = array_reverse( $collected );
        return $footnote;
    }

    /**
     * Reduce multiline blockquote nodes.
     *
     * Reduce multline block quote nodes, which are not already closed by line
     * endings.
     *
     * @param ezcDocumentWikiBlockquoteNode $node
     * @return mixed
     */
    protected function reduceBlockquoteNode( ezcDocumentWikiBlockquoteNode $node )
    {
        // Collect inline nodes
        $collected = array();
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0] instanceof ezcDocumentWikiInlineNode ) &&
                ( ( !$this->documentStack[0] instanceof ezcDocumentWikiBlockquoteNode ) ||
                  ( $this->documentStack[0]->nodes !== array() ) ) )
        {
            array_unshift( $collected, array_shift( $this->documentStack ) );
        }

        if ( isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0] instanceof ezcDocumentWikiBlockquoteNode ) &&
             ( $this->documentStack[0]->nodes === array() ) )
        {
            // We found an empty matching node. Reduce
            $blockquote = array_shift( $this->documentStack );
            $blockquote->nodes = $collected;
            return $blockquote;
        }
        else
        {
            // No matching node found, just leave it on the stack.
            $this->documentStack = array_merge( array_reverse( $collected ), $this->documentStack );
            return $node;
        }
    }

    /**
     * Reduce bullet list items to list.
     *
     * Reduce list items to lists, and create new wrapping list nodes.
     *
     * @param ezcDocumentWikiBlockLevelNode $node
     * @return mixed
     */
    protected function reduceBulletListItem( ezcDocumentWikiBlockLevelNode $node )
    {
        // Do not reduce empty bullet list nodes, but wait until they are
        // filled
        if ( $node->nodes === array() )
        {
            return $node;
        }

        // If there is not already a list node which matches the properties of
        // the current list item, create a new bullet list.
        if ( !isset( $this->documentStack[0] ) ||
             ( !$this->documentStack[0] instanceof ezcDocumentWikiBulletListNode ) ||
             ( $this->documentStack[0]->level !== $node->level ) )
        {
            $list = new ezcDocumentWikiBulletListNode( $node->token );
        }
        else
        {
            $list = array_shift( $this->documentStack );
        }

        $list->nodes = array_merge( $list->nodes, array( $node ) );
        return $list;
    }

    /**
     * Reduce enumerated list items to list.
     *
     * Reduce list items to lists, and create new wrapping list nodes.
     *
     * @param ezcDocumentWikiBlockLevelNode $node
     * @return mixed
     */
    protected function reduceEnumeratedListItem( ezcDocumentWikiBlockLevelNode $node )
    {
        // Do not reduce empty enumerated list nodes, but wait until they are
        // filled
        if ( $node->nodes === array() )
        {
            return $node;
        }

        // If there is not already a list node which matches the properties of
        // the current list item, create a new bullet list.
        if ( !isset( $this->documentStack[0] ) ||
             ( !$this->documentStack[0] instanceof ezcDocumentWikiEnumeratedListNode ) ||
             ( $this->documentStack[0]->level !== $node->level ) )
        {
            $list = new ezcDocumentWikiEnumeratedListNode( $node->token );
        }
        else
        {
            $list = array_shift( $this->documentStack );
        }

        $list->nodes = array_merge( $list->nodes, array( $node ) );
        return $list;
    }

    /**
     * Merge lists recusively.
     *
     * Merge lists recusively
     *
     * @param array $lists
     * @return ezcDocumentWikiListNode
     */
    protected function mergeListRecursively( array $lists )
    {
        $list      = array_shift( $lists );
        $collected = array();
        while ( $child = array_shift( $lists ) )
        {
            if ( $child->level > $list->level )
            {
                array_unshift( $collected, $child );
            }
            else
            {
                if ( count( $collected ) )
                {
                    $list->nodes[] = $this->mergeListRecursively( $collected );
                    $collected     = array();
                }

                $list->nodes = array_merge(
                    $list->nodes,
                    $child->nodes
                );
            }
        }

        return $list;
    }

    /**
     * Reduce lists.
     *
     * Stack lists with higher indentation into each other and merge multiple
     * lists of same type and indentation.
     *
     * @param ezcDocumentWikiBlockLevelNode $node
     * @return mixed
     */
    protected function reduceLists( ezcDocumentWikiBlockLevelNode $node )
    {
        $collected       = array();
        $documentStack   = array();
        $class           = null;
        $lastIndentation = 1;

        while ( $child = array_shift( $this->documentStack ) )
        {
            /* DEBUG
            echo "   -> Found ", get_class( $child ), " - current indentation: $lastIndentation.\n";
            // /DEBUG */

            // Clean up, on:
            // - List nodes have already be found, AND
            // - Not a list node
            // - A list node of a different type on the same or lower level
            if ( ( !$child instanceof ezcDocumentWikiListNode ) ||
                 ( ( $class !== null ) &&
                   ( ( !$child instanceof ezcDocumentWikiListNode ) ||
                     ( ( !$child instanceof $class ) &&
                       ( $child->level <= $lastIndentation ) ) ) ) )
            {
                if ( count( $collected ) )
                {
                    /* DEBUG
                    echo "     -> Merge lists: ", count( $collected ), " entries.\n";
                    // /DEBUG */
                    $documentStack[] = $this->mergeListRecursively( $collected );
                    $class     = null;
                    $collected = array();
                }

                if ( !$child instanceof ezcDocumentWikiListNode )
                {
                    /* DEBUG
                    echo "     -> Skip element.\n";
                    // /DEBUG */
                    $documentStack[] = $child;
                    continue;
                }
            }

            // Collect all belonging lists
            if ( ( $class === null ) ||
                 ( $child instanceof $class ) )
            {
                $class           = get_class( $child );
                $lastIndentation = $child->level;
            }
            array_unshift( $collected, $child );
            /* DEBUG
            echo "     -> Collect element ($class, $lastIndentation).\n";
            // /DEBUG */
        }

        // Merge, when end of document is reached
        if ( count( $collected ) )
        {
            /* DEBUG
            echo "     -> Merge lists: ", count( $collected ), " entries.\n";
            // /DEBUG */
            $documentStack[] = $this->mergeListRecursively( $collected );
        }
        $this->documentStack = $documentStack;

        return $node;
    }

    /**
     * Reduce table rows.
     *
     * Reduce the nodes aagregated for one table row into table cells, and
     * merge the table rows into table nodes.
     *
     * @param ezcDocumentWikiTableRowNode $node
     * @return mixed
     */
    protected function reduceTableRow( ezcDocumentWikiTableRowNode $node )
    {
        // We only care about table rows which already have some contents
        // assigned.
        if ( $node->nodes === array() )
        {
            return $node;
        }

        $cells      = array();
        $separators = array();
        $cell       = ( $node->nodes[0] instanceof ezcDocumentWikiSeparatorNode ) ? -1 : 0;
        foreach ( $node->nodes as $child )
        {
            if ( $child instanceof ezcDocumentWikiSeparatorNode )
            {
                $separators[++$cell] = $child;
                continue;
            }

            $cells[$cell][] = $child;
        }

        // Transform aggregated contents in cell nodes
        foreach ( $cells as $nr => $cellNodes )
        {
            $cells[$nr] = new ezcDocumentWikiTableCellNode( $separators[$nr]->token );
            $cells[$nr]->nodes = $cellNodes;
        }
        $node->nodes = $cells;

        // Merge the table row into a table.
        if ( !isset( $this->documentStack[0] ) ||
             ( !$this->documentStack[0] instanceof ezcDocumentWikiTableNode ) )
        {
            $table = new ezcDocumentWikiTableNode( $node->token );
        }
        else
        {
            $table = array_shift( $this->documentStack );
        }
        $table->nodes[] = $node;

        return $table;
    }
}

?>
