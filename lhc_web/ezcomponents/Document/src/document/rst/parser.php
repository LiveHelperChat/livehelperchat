<?php
/**
 * File containing the ezcDocumentRstParser
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parser for RST documents
 *
 * RST is not describable by a context free grammar, so that the common parser
 * approaches won't work.
 *
 * Parser basics
 * -------------
 *
 * We decided to implement a parser roughly following the scheme of common
 * shift reduce parsers with a dynamic lookahead.
 *
 * - Shifting:
 *
 *   The shift step commonly tries to convert a token or a sequence of tokens
 *   to the respective AST node. In the case of RST we may need a dynamic
 *   lookahead to decide which type of AST node a token relates to, which is
 *   different from common LRn parsers.
 *
 *   There is a map of parser tokens to internal methods for callbacks, which
 *   are called in the defined order if the main parser methods reach the
 *   respective token in the provided token array. Each shift method is called
 *   with the relating token and the array of subsequent, yet unhandled,
 *   tokens.
 *
 *   These methods are expected to return either false, if the current token
 *   cannot be shifted by the called rule, true, when the token has been
 *   handled, but no document node has been created from it or a new
 *   ezcDocumentRstNode object, which is some AST node. When a shift method
 *   returned false the next shift method in the array is called to handle the
 *   token.
 *
 *   The returned ezcDocumentRstNode objects are put on the document stack in
 *   the order they are found in the token array.
 *
 * - Reducing:
 *
 *   The reduce step commonly tries to reduce matching structures, like finding
 *   the matching opening brace, when a closing brace has been added to the
 *   document stack. In this case all nodes between the two braces are
 *   aggregated into the brace node, so that a tree is created.
 *
 *   The reductions array defines an array with a mapping of node types to
 *   rection callbacks, which are called if such a node has been added to the
 *   document stack. Each reduction method may either return false, if it could
 *   not handle the given node, or a new node. The reduction methods often
 *   manipulate the document stack, like searching backwards and aggregating
 *   nodes.
 *
 *   If a reduction method returns a node the parser reenters the reduction
 *   process with the new node.
 *
 * The state of the RST parser heavily depends on the current indentation
 * level, which is stored in the class property $indentation, and mainly
 * modified in the special shift method updateIndentation(), which is called on
 * each line break token.
 *
 * Some of the shift methods aggregate additional tokens from the token array,
 * bypassing the main parser method. This should only be done, if no common
 * handling is required for the aggregated tokens.
 *
 * Tables
 * ------
 *
 * The handling of RST tables is quite complex and the affiliation of tokens to
 * nodes depend on the line and character position of the token. In this case
 * the tokens are first aggregated into their cell contexts and reenter the
 * parser afterwards.
 *
 * For token lists, which are required to reenter the parser - independently
 * from the current global parser state - the method reenterParser() takes such
 * token lists, removes the overall indentation and returns a new document of
 * the provided token array.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstParser extends ezcDocumentParser
{
    /**
     * Current indentation of a paragraph / lsit item.
     *
     * @var int
     */
    protected $indentation = 0;

    /**
     * For the special case of dense bullet lists we need to update the
     * indetation right after we created a new paragraph in one action. We
     * store the indetation to update past the paragraph creation in this case
     * in this variable.
     *
     * @var int
     */
    protected $postIndentation = null;

    /**
     * Array containing simplified shift ruleset
     *
     * We cannot express the RST syntax as a usual grammar using a BNF. With
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
        ezcDocumentRstToken::WHITESPACE => array(

            // This should always be the last rule in this section: We shift
            // the whitespace, which could not be recognized as something else,
            // as text.
            'shiftWhitespaceAsText',
        ),
        ezcDocumentRstToken::NEWLINE => array(
            'shiftParagraph',
            'updateIndentation',
            'shiftAsWhitespace',
        ),
        ezcDocumentRstToken::BACKSLASH => array(
            'shiftBackslash',
        ),
        ezcDocumentRstToken::SPECIAL_CHARS => array(
            'shiftTitle',
            'shiftTransition',
            'shiftLineBlock',
            'shiftInlineLiteral',
            'shiftInlineMarkup',
            'shiftInterpretedTextMarkup',
            'shiftReference',
            'shiftAnonymousHyperlinks',
            'shiftExternalReference',
            'shiftBlockquoteAnnotation',
            'shiftBulletList',
            'shiftEnumeratedList',
            'shiftLiteralBlock',
            'shiftComment',
            'shiftAnonymousReference',
            'shiftFieldList',
            'shiftSimpleTable',
            'shiftGridTable',

            // This should always be the last rule in this section: We shift
            // special character groups, which could not be recognized as
            // something else, as text.
            'shiftSpecialCharsAsText',
        ),
        ezcDocumentRstToken::TEXT_LINE => array(
            'shiftEnumeratedList',
            'shiftText',
        ),
        ezcDocumentRstToken::EOF => array(
            'shiftDocument',
        ),
    );

    /**
     * Array containing simplified reduce ruleset
     *
     * We cannot express the RST syntax as a usual grammar using a BNF. This
     * structure implements a pseudo grammar by assigning a number of callbacks
     * for internal methods implementing reduction rules for a detected syntax
     * element.
     *
     * <code>
     *  array(
     *      ezcDocumentRstNode::DOCUMENT => 'reduceDocument'
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $reductions = array(
        // Also for ezcDocumentRstNode::SECTION, since the constants point to
        // the same value.
        ezcDocumentRstNode::DOCUMENT            => array(
            'reduceList',
            'reduceSection',
        ),
        ezcDocumentRstNode::TITLE               => array(
            'reduceTitle',
        ),
        ezcDocumentRstNode::PARAGRAPH           => array(
            'reduceParagraph',
            'reduceListItem',
            'reduceBlockquoteAnnotationParagraph',
            'reduceBlockquote',
        ),
        ezcDocumentRstNode::COMMENT             => array(
            'reduceListItem',
        ),
        ezcDocumentRstNode::DIRECTIVE           => array(
            'reduceListItem',
        ),
        ezcDocumentRstNode::LITERAL_BLOCK       => array(
            'reduceListItem',
        ),
        ezcDocumentRstNode::BULLET_LIST         => array(
            'reduceList',
        ),
        ezcDocumentRstNode::ENUMERATED_LIST     => array(
            'reduceList',
        ),

        ezcDocumentRstNode::ANNOTATION          => array(
            'reduceBlockquoteAnnotation',
        ),

        ezcDocumentRstNode::MARKUP_EMPHASIS     => array(
            'reduceMarkup',
        ),
        ezcDocumentRstNode::MARKUP_STRONG       => array(
            'reduceMarkup',
        ),
        ezcDocumentRstNode::MARKUP_INTERPRETED  => array(
            'reduceInterpretedText',
            'reduceInternalTarget',
        ),
        ezcDocumentRstNode::MARKUP_SUBSTITUTION => array(
            'reduceMarkup',
        ),

        ezcDocumentRstNode::REFERENCE           => array(
            'reduceReference',
        ),

        ezcDocumentRstNode::LINK_ANONYMOUS      => array(
            'reduceLink',
        ),
        ezcDocumentRstNode::LINK_REFERENCE      => array(
            'reduceLink',
        ),
    );

    /**
     * List of node types, which can be considered as inline text nodes.
     *
     * @var array
     */
    protected $textNodes = array(
        ezcDocumentRstNode::TEXT_LINE,
        ezcDocumentRstNode::MARKUP_EMPHASIS,
        ezcDocumentRstNode::MARKUP_STRONG,
        ezcDocumentRstNode::MARKUP_INTERPRETED,
        ezcDocumentRstNode::MARKUP_LITERAL,
        ezcDocumentRstNode::MARKUP_SUBSTITUTION,
        ezcDocumentRstNode::LINK_ANONYMOUS,
        ezcDocumentRstNode::LINK_REFERENCE,
        ezcDocumentRstNode::REFERENCE,
        ezcDocumentRstNode::TARGET,
    );

    /**
     * List of node types, which are valid block nodes, where we can
     * indentation changes after, or which can be aggregated into sections.
     *
     * @var array
     */
    protected $blockNodes = array(
        ezcDocumentRstNode::PARAGRAPH,
        ezcDocumentRstNode::BLOCKQUOTE,
        ezcDocumentRstNode::SECTION,
        ezcDocumentRstNode::BULLET_LIST,
        ezcDocumentRstNode::ENUMERATED_LIST,
        ezcDocumentRstNode::TABLE,
        ezcDocumentRstNode::LITERAL_BLOCK,
        ezcDocumentRstNode::COMMENT,
        ezcDocumentRstNode::DIRECTIVE,
        ezcDocumentRstNode::SUBSTITUTION,
        ezcDocumentRstNode::NAMED_REFERENCE,
        ezcDocumentRstNode::FOOTNOTE,
        ezcDocumentRstNode::ANON_REFERENCE,
        ezcDocumentRstNode::TRANSITION,
        ezcDocumentRstNode::FIELD_LIST,
        ezcDocumentRstNode::DEFINITION_LIST,
        ezcDocumentRstNode::LINE_BLOCK,
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
     * @var ezcDocumentRstStack
     */
    protected $documentStack = null;

    /**
     * Array with title levels used by the document author in their order.
     *
     * @var array
     */
    protected $titleLevels = array();

    /**
     * List of builtin directives, which do not aggregate more text the
     * parameters and options. User defined directives always aggregate
     * following indeted text.
     *
     * @var array
     */
    protected $shortDirectives = array(
        'note',
        'notice',
        'warning',
        'danger',
        'image',
    );

    /**
     * PCRE regular expression for detection of URLs in texts.
     */
    const REGEXP_INLINE_LINK = '(
        (?:^|[\s,.!?])
            (?# Ignore matching braces around the URL)
                (<)?
                    (\[)?
                        (\()?
                            (?# Ignore quoting around the URL)
                            ([\'"]?)
                                (?# Actually match the URL)
                                (?P<match>
                                    (?P<url>[a-z]+://[^\s]*?) |
                                    (?:mailto:)?(?P<mail>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})
                                )
                            \4
                        (?(3)\))
                    (?(2)\])
                (?(1)>)
            (?# Ignore common punctuation after the URL)
        [.,?!]?(?:\s|$)
    )Sxm';

    /**
     * Construct new document
     *
     * @param ezcDocumentParserOptions $options
     */
    public function __construct( ezcDocumentParserOptions $options = null )
    {
        parent::__construct( $options );
        $this->documentStack = new ezcDocumentRstStack();
    }

    /**
     * Shift- / reduce-parser for RST token stack
     *
     * @param array $tokens
     * @return void
     */
    public function parse( array $tokens )
    {
        $tokens = new ezcDocumentRstStack( $tokens );
        /* DEBUG
        echo "\n\nStart parser\n============\n\n";
        // /DEBUG */

        while ( ( $token = $tokens->shift() ) !== null )
        {
            /* DEBUG
            echo "[T] Token: " . ezcDocumentRstToken::getTokenName( $token->type ) . " ({$token->type}) at {$token->line}:{$token->position}.\n";
            // /DEBUG */

            // First shift given token by the defined reduction methods
            foreach ( $this->shifts[$token->type] as $method )
            {
                /* DEBUG
                echo " [S] Try $method\n";
                // /DEBUG */
                if ( ( $node = $this->$method( $token, $tokens ) ) === false )
                {
                    // The shift method cannot handle the token, go to next
                    continue;
                }
                /* DEBUG
                echo " [=> Skip used.\n";
                // /DEBUG */

                if ( $node === true )
                {
                    // The shift method handled the token, but did not return a
                    // new node, we just go to the next token...
                    continue 2;
                }

                // Call reduce methods for nodes as long the reduction methods
                // recreate some node
                $ruleNumber = 0;
                $ruleType = 0;
                do {
                    // Reset the rule counter on changes of the node type
                    if ( $ruleType !== $node->type )
                    {
                        $ruleNumber = 0;
                    }

                    if ( !isset( $this->reductions[$node->type] ) ||
                         !isset( $this->reductions[$node->type][$ruleNumber] ) )
                    {
                        // If there are no reduction rules for the node, just
                        // add it to the stack
                        /* DEBUG
                        echo "  [R] Add '" . ezcDocumentRstNode::getTokenName( $node->type ) . "' to stack (" . ( count( $this->documentStack ) + 1 ) . " elements).\n";
                        // /DEBUG */

                        $this->documentStack->unshift( $node );

                        break;
                    }

                    $ruleType = $node->type;
                    $reduction = $this->reductions[$node->type][$ruleNumber++];
                    /* DEBUG
                    echo "  [R] Reduce with $reduction.\n";
                    // /DEBUG */
                    $node = $this->$reduction( $node );
                } while ( $node !== null );

                // We found a matching rule, so that we can leave the loop
                break;
            }
        }

        // Check if we successfully reduced the document stack
        if ( ( count( $this->documentStack ) !== 1 ) ||
             ( !( $document = $this->documentStack->rewind() ) instanceof ezcDocumentRstDocumentNode ) )
        {
            $node = isset( $document ) ? $document : $this->documentStack->rewind();
            $this->triggerError(
                E_PARSE,
                'Expected end of file, got: ' . ezcDocumentRstNode::getTokenName( $node->type ) . ".",
                null, null, null
            );
        }

        /* DEBUG
        // echo "\nResulting document:\n\n", $document->dump(), "\nTest result: ";
        // /DEBUG */
        return $document;
    }

    /**
     * Re-align tokens
     *
     * Realign tokens, so that the line start positions start at 0 again, even 
     * they were indeted before.
     * 
     * @param array $tokens 
     * @return array
     */
    protected function realignTokens( $tokens )
    {
        /* DEBUG
        static $c = 0;
        file_put_contents( "tokens-reentered-$c.php", "<?php\n\n return " . var_export( $tokens, true ) . ";\n\n" );
        // /DEBUG */

        $firstToken  = reset( $tokens );
        if ( ( $firstToken->type === ezcDocumentRstToken::WHITESPACE ) &&
             ( $firstToken->position === 1 ) )
        {
            $offset = strlen( $firstToken->content );
        }
        else
        {
            $offset = $firstToken->position - ( $firstToken->type === ezcDocumentRstToken::WHITESPACE ? 0 : 1 );
        }

        $fixedTokens = array();
        $lineOffset  = $offset;
        foreach ( $tokens as $nr => $token )
        {
            if ( ( $token->type === ezcDocumentRstToken::WHITESPACE ) &&
                 isset( $tokens[$nr + 1] ) &&
                 ( $tokens[$nr + 1]->type === ezcDocumentRstToken::WHITESPACE ) )
            {
                // Skip multiple whitespace tokens in a row.
                continue;
            }

            if ( $token->type === ezcDocumentRstToken::NEWLINE )
            {
                $lineOffset = false;
            }
            elseif ( $lineOffset === false )
            {
                if ( ( $firstToken->type === ezcDocumentRstToken::WHITESPACE ) &&
                     ( $firstToken->position === 1 ) )
                {
                    $lineOffset = min( $offset, strlen( $firstToken->content ) );
                }
                else
                {
                    $lineOffset = min( $offset, $token->position - ( $token->type === ezcDocumentRstToken::WHITESPACE ? 0 : 1 ) );
                }
            }

            if ( ( $token->type === ezcDocumentRstToken::WHITESPACE ) &&
                 ( $token->position <= $lineOffset ) )
            {
                if ( strlen( $token->content ) <= $lineOffset )
                {
                    // Just skip token, completely out of tokens bounds
                    continue;
                }
                else
                {
                    // Shorten starting whitespace token
                    $token = clone $token;
                    $token->position = 1;
                    $token->content = substr( $token->content, $lineOffset );
                    $fixedTokens[] = $token;
                }
            }
            else
            {
                $token = clone $token;
                $token->position = max( 1, $token->position - $lineOffset );
                $fixedTokens[] = $token;
            }
        }

        // If required add a second newline, if the provided token array does
        // not contain any newlines at the end.
        if ( $token->type !== ezcDocumentRstToken::NEWLINE )
        {
            $fixedTokens[] = new ezcDocumentRstToken( ezcDocumentRstToken::NEWLINE, "\n", null, null );
        }

        $fixedTokens[] = new ezcDocumentRstToken( ezcDocumentRstToken::NEWLINE, "\n", null, null );
        $fixedTokens[] = new ezcDocumentRstToken( ezcDocumentRstToken::EOF, null, null, null );

        /* DEBUG
        file_put_contents( "tokens-reentered-$c-fixed.php", "<?php\n\n return " . var_export( $fixedTokens, true ) . ";\n\n" );
        ++$c;
        // /DEBUG */

        return $fixedTokens;
    }

    /**
     * Reenter parser with a list of tokens
     *
     * Returns a parsed document created from the given tokens. With optional,
     * but default, reindetation of the tokens relative to the first token.
     *
     * @param array $tokens
     * @param bool $reindent
     * @return ezcDocumentRstDocumentNode
     */
    protected function reenterParser( array $tokens, $reindent = true )
    {
        if ( count( $tokens ) < 1 )
        {
            return array();
        }

        // Fix indentation for all cell tokens, as they were a single document.
        $fixedTokens = $reindent ? $this->realignTokens( $tokens ) : $tokens;

        $parser = new ezcDocumentRstParser();
        return $parser->parse( $fixedTokens );
    }

    /**
     * Print a dump of the document stack
     *
     * This function is only for use during dubbing of the document stack
     * structure.
     *
     * @return void
     */
    protected function dumpStack()
    {
        foreach ( $this->documentStack as $nr => $node )
        {
            printf( "% 2d) %s\n", $nr, $node->dump() );
        }
    }

    /**
     * Update the current indentation after each newline.
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return bool
     */
    protected function updateIndentation( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        // Indentation Whitespaces right after a title line are irrelevant and
        // should just be skipped as text, so ignore this rule for them:
        if ( ( isset( $this->documentStack[0] ) ) &&
             ( $this->documentStack[0]->type === ezcDocumentRstNode::TITLE ) )
        {
            return false;
        }

        if ( isset( $tokens[0] ) &&
             ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) )
        {
            // Remove the whitespace from the stack, as it is only for
            // indentation and should not be converted to text.
            $whitespace = $tokens->shift();

            if ( isset( $tokens[0] ) &&
                 ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) )
            {
                // This is just a blank line
                /* DEBUG
                echo '   -> Empty line.';
                // /DEBUG */
                return false;
            }

            $indentation = iconv_strlen( $whitespace->content, 'UTF-8' );
        }
        elseif ( isset( $tokens[0] ) &&
                 ( $tokens[0]->position > 1 ) )
        {
            // While reparsing table cell contents we may miss some whitespaces
            // and directly get an indented non-whitespace node, which is
            // sufficant to also determine the indetation.
            $indentation = $tokens[0]->position - 1;
            $whitespace = false;
        }
        else
        {
            // No whitespace means an indentation level of 0
            $indentation = 0;
        }

        // This is the special case for dense bullet lists. In case of bullet
        // lists the indentation may also change right between two lines
        // without an additional newline.
        if ( isset( $this->documentStack[0] ) &&
             ( in_array( $this->documentStack[0]->type, $this->textNodes, true ) ) &&
             ( isset( $tokens[0] ) ) &&
             ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
             ( in_array( $tokens[0]->content, array(
                    '*', '-', '+',
                    "\xe2\x80\xa2", "\xe2\x80\xa3", "\xe2\x81\x83"
               ) ) ) &&
             isset( $tokens[1] ) &&
             ( $tokens[1]->type === ezcDocumentRstToken::WHITESPACE ) )
        {
            // We update the indentation in this case and add a paragraph node
            // to close the prior paragraph.
            $this->postIndentation = $indentation;
            /* DEBUG
            echo "   -> Bullet special paragraph update\n";
            echo "   => Updated indentation to {$indentation}.\n";
            // /DEBUG */
            $paragraph = new ezcDocumentRstParagraphNode( $token );
            /* DEBUG
            echo "   => Paragraph post indentation set to {$paragraph->indentation}.\n";
            // /DEBUG */
            return $paragraph;
        }

        // There is also a special case for dense enumeration lists, very
        // similar to bullet lists.
        if ( isset( $this->documentStack[0] ) &&
             ( in_array( $this->documentStack[0]->type, $this->textNodes, true ) ) &&
             ( $this->isEnumeratedList( $tokens ) ) )
        {
            // We update the indentation in this case and add a paragraph node
            // to close the prior paragraph.
            $this->postIndentation = $indentation;
            /* DEBUG
            echo "   -> Enumeration list special paragraph update\n";
            echo "   => Updated indentation to {$indentation}.\n";
            // /DEBUG */
            $paragraph = new ezcDocumentRstParagraphNode( $token );
            /* DEBUG
            echo "   => Paragraph post indentation set to {$paragraph->indentation}.\n";
            // /DEBUG */
            return $paragraph;
        }

        // If the current indentation is 0 and the indentation increased, with
        // text line nodes as last items on the stack we got a definition list.
        if ( ( $this->indentation === 0 ) &&
             ( $indentation > $this->indentation ) &&
             ( isset( $this->documentStack[0] ) ) &&
             ( in_array( $this->documentStack[0]->type, $this->textNodes, true ) ) )
        {
            /* DEBUG
            echo "  => Definition list detected.\n";
            // /DEBUG */
            // Put indetation token back into the token stack.
            if ( $whitespace !== false )
            {
                $tokens->unshift( $whitespace );
            }
            return $this->shiftDefinitionList( $token, $tokens );
        }

        // The indentation may only change after we reduced a block level
        // element. There may be other spcial cases, which are handled
        // elsewhere.
        if ( ( $this->indentation !== $indentation ) &&
             isset( $this->documentStack[0] ) &&
             !in_array( $this->documentStack[0]->type, $this->blockNodes, true ) )
        {
            // Enumerated lists are relabeled to paragraphs, if the indentation
            // level is changed right after the list item seemed to start.
            for ( $i = 0; $i < count( $this->documentStack ); ++$i )
            {
                if ( in_array( $this->documentStack[$i]->type, $this->textNodes, true ) )
                {
                    // This is just text, we are looking for the next block level element.
                    continue;
                }

                // If the last block level element was a enumerated list, just relabel.
                if ( $this->documentStack[$i]->type === ezcDocumentRstNode::ENUMERATED_LIST )
                {
                    // Readd enumeration lsit marker as plain text.
                    $textNode = new ezcDocumentRstTextLineNode( $this->documentStack[$i]->token );
                    $textNode->token->content = $this->documentStack[$i]->text;
                    $this->documentStack[$i] = $textNode;

                    // Reset indentation level.
                    $this->indentation = $indentation;
                    $this->postIndentation = null;
                    return false;
                }
            }

            $this->triggerError(
                E_PARSE,
                "Unexpected indentation change from level {$this->indentation} to {$indentation}.",
                null, $token->line, $token->position
            );
        }

        // Otherwise indentation changes are fine, and we just update the
        // current indentation level for later checks
        $this->indentation = $indentation;
        $this->postIndentation = null;
        /* DEBUG
        echo "   => Updated indentation to {$indentation}.\n";
        // /DEBUG */
        return false;
    }

    /**
     * Create new document node
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstDocumentNode
     */
    protected function shiftDocument( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        // If there are any tokens left after the end of the file, something
        // went seriously wrong in the tokenizer.
        if ( count( $tokens ) )
        {
            $this->triggerError(
                E_PARSE,
                'Unexpected end of file.',
                null, $token->line, $token->position
            );
        }

        $this->documentStack->push( new ezcDocumentRstDocumentNode() );
        return new ezcDocumentRstDocumentNode();
    }

    /**
     * Escaping of special characters
     *
     * A backslash is used for character escaping, as defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#escaping-mechanism
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTitleNode
     */
    protected function shiftBackslash( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( isset( $tokens[0] ) )
        {
            switch ( $tokens[0]->type )
            {
                case ezcDocumentRstToken::NEWLINE:
                case ezcDocumentRstToken::WHITESPACE:
                    // Escaped whitespace characters are just removed, just
                    // like the backslash itself.
                    $tokens->shift();
                    /* DEBUG
                    echo "  -> Remove escaped whitespace.\n";
                    // /DEBUG */
                    return true;

                case  ezcDocumentRstToken::BACKSLASH:
                    // A double backslash results in a backslash text node.
                    $tokens[0]->type = ezcDocumentRstToken::TEXT_LINE;
                    $tokens[0]->escaped = true;
                    /* DEBUG
                    echo "  -> Transformed backslash to text.\n";
                    // /DEBUG */
                    return true;

                case ezcDocumentRstToken::SPECIAL_CHARS:
                case ezcDocumentRstToken::TEXT_LINE:
                    // A special character group is always escaped by a
                    // preceeding backslash
                    if ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) > 1 )
                    {
                        // Long special character group, so that we need to
                        // split it up.
                        $newToken = new ezcDocumentRstToken(
                            ezcDocumentRstToken::TEXT_LINE,
                            $tokens[0]->content[0], $tokens[0]->line, $tokens[0]->position
                        );
                        $newToken->escaped = true;

                        // Remove extracted part from old token
                        $tokens[0]->content = substr( $tokens[0]->content, 1 );
                        ++$tokens[0]->position;

                        // Add new token to the beginning of the token stack.
                        /* DEBUG
                        echo "  -> Add new split token.\n";
                        // /DEBUG */
                        $tokens->unshift( $newToken );
                        return true;
                    }
                    else
                    {
                        // Just convert token into a simple text node
                        /* DEBUG
                        echo "  -> Transformed token into escaped text.\n";
                        // /DEBUG */
                        $tokens[0]->type = ezcDocumentRstToken::TEXT_LINE;
                        $tokens[0]->escaped = true;
                        return true;
                    }
            }
        }
    }

    /**
     * Create new title node from titles with a top and bottom line
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTitleNode
     */
    protected function shiftTitle( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->position !== 1 ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) )
        {
            // This is not a title line at all
            return false;
        }

        // Handle literal block markers differently, they are followed by two
        // newlines (maybe with whitespaces inbetween).
        if ( ( ( $tokens[1]->type === ezcDocumentRstToken::NEWLINE ) ||
               ( ( $tokens[1]->type === ezcDocumentRstToken::WHITESPACE ) &&
                 ( $tokens[2]->type === ezcDocumentRstToken::NEWLINE ) ) ||
               ( iconv_strlen( $tokens[1]->content, 'UTF-8' ) > iconv_strlen( $token->content, 'UTF-8' ) ) ||
               ( ( $tokens[1]->type === ezcDocumentRstToken::WHITESPACE ) &&
                 ( isset( $tokens[2] ) ) &&
                 ( ( iconv_strlen( $tokens[1]->content, 'UTF-8' ) + iconv_strlen( $tokens[2]->content, 'UTF-8' ) ) > iconv_strlen( $token->content, 'UTF-8' ) ) ) ) &&
             isset( $this->documentStack[0] ) &&
             !in_array( $this->documentStack[0]->type, $this->textNodes, true ) )
        {
            // This seems to be something else, like a liteal block marker.
            return false;
        }

        return new ezcDocumentRstTitleNode(
            $token
        );
    }

    /**
     * Shift transistions, which are separators in the document.
     *
     * Transitions are specified here:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#transitions
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTitleNode
     */
    protected function shiftTransition( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->position !== 1 ) ||
             ( $token->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( iconv_strlen( $token->content, 'UTF-8' ) < 4 ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ||
             ( !isset( $tokens[1] ) ) ||
             ( $tokens[1]->type !== ezcDocumentRstToken::NEWLINE ) )
        {
            // This is not a transistion
            return false;
        }

        return new ezcDocumentRstTransitionNode(
            $token
        );
    }

    /**
     * Shift line blocks
     *
     * Shift line blocks, which are specified at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#line-blocks
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTitleNode
     */
    protected function shiftLineBlock( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->position !== ( $this->indentation + 1 ) ) ||
             ( $token->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $token->content !== '|' ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE ) )
        {
            // This is not a line block
            return false;
        }

        // Put everything back into the token list, as this makes it easier for
        // us to read
        $lines = array();
        $tokens->unshift( $token );
        if ( $this->indentation > 0 )
        {
            $tokens->unshift( new ezcDocumentRstToken(
                ezcDocumentRstToken::WHITESPACE,
                str_repeat( ' ', $this->indentation ),
                $token->line, 1
            ) );
        }

        // Each line is introduced by '| ', optionally with the proper current
        // indentation.
        while ( ( ( $this->indentation === 0 ) ||
                  ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
                    ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) === $this->indentation ) ) &&
                    ( $tokens[1]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                    ( $tokens[1]->content === '|' ) &&
                    ( ( $tokens[2]->type === ezcDocumentRstToken::WHITESPACE ) ||
                      ( $tokens[2]->type === ezcDocumentRstToken::NEWLINE ) ) ) &&
                ( ( $this->indentation > 0 ) ||
                  ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                    ( $tokens[0]->content === '|' ) &&
                    ( ( $tokens[1]->type === ezcDocumentRstToken::WHITESPACE ) ||
                      ( $tokens[1]->type === ezcDocumentRstToken::NEWLINE ) ) ) ) )
        {
            /* DEBUG
            echo "  -> Next line: {$tokens[0]->line}\n";
            // /DEBUG */
            if ( $this->indentation > 0 )
            {
                // Skip the indentation token, which length has already been
                // checked.
                $tokens->shift();
            }

            // Shift the line block marker
            $line = array( $tokens->shift() );

            $whitespace = $tokens->shift();
            if ( $whitespace->type === ezcDocumentRstToken::NEWLINE )
            {
                // Properly handle empty line in line blocks.
                /* DEBUG
                echo "   -> Skip empty line.\n";
                // /DEBUG */
                $lines[] = $line;
                continue;
            }

            // Remove the leading space from the following whitespace token
            if ( $whitespace->content !== ' ' )
            {
                /* DEBUG
                echo "   -> Shorten indentation.\n";
                // /DEBUG */
                $whitespace->content = substr( $whitespace->content, 1 );
                $line[] = $whitespace;
            }

            // Read all tokens in current line und following lines.
            /* DEBUG
            echo "   -> Read line block line tokens\n";
            // /DEBUG */
            do {
                /* DEBUG
                echo "    -> Read tokens: ";
                // /DEBUG */
                do {
                    $line[] = $token = $tokens->shift();
                    /* DEBUG
                    echo ".";
                    // /DEBUG */
                } while ( ( $token->type !== ezcDocumentRstToken::NEWLINE ) &&
                          isset( $tokens[0] ) );
                /* DEBUG
                echo "\n";
                // /DEBUG */

            } while ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
                      ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) >= ( $this->indentation + 2 ) ) &&
                      ( $tokens->shift() ) );
            $lines[] = $line;
        }

        // Transform aggregated tokens in proper AST structures
        $node = new ezcDocumentRstLineBlockNode( $token );
        $node->indentation = $this->indentation;
        foreach ( $lines as $line )
        {
            $lineNode = new ezcDocumentRstLineBlockLineNode( array_shift( $line ) );
            foreach ( $line as $token )
            {
                $lineNode->nodes[] = new ezcDocumentRstLiteralNode( $token );
            }
            $node->nodes[] = $lineNode;
        }

        // Rest the indentation and exit
        $this->indentation = 0;
        return $node;
    }

    /**
     * Just keep text as text nodes
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTitleNode
     */
    protected function shiftText( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        return new ezcDocumentRstTextLineNode(
            $token
        );
    }

    /**
     * Shift a paragraph node on two newlines
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTitleNode
     */
    protected function shiftParagraph( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( !isset( $tokens[0] ) ||
               ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ) &&
             ( !isset( $tokens[0] ) || !isset( $tokens[1] ) ||
               ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE ) ||
               ( $tokens[1]->type !== ezcDocumentRstToken::NEWLINE ) ) )
        {
            // For now we only check for paragraphs closed with two newlines.
            /* DEBUG
            echo "  -> No following newline.\n";
            // /DEBUG */
            return false;
        }

        // Remove all following newlines except the last one.
        while ( ( isset( $tokens[1] ) &&
                  ( $tokens[1]->type === ezcDocumentRstToken::NEWLINE ) ) ||
                ( isset( $tokens[1] ) && isset( $tokens[2] ) &&
                  ( $tokens[1]->type === ezcDocumentRstToken::WHITESPACE ) &&
                  ( $tokens[2]->type === ezcDocumentRstToken::NEWLINE ) ) )
        {
            $tokens->shift();
        }

        return new ezcDocumentRstParagraphNode(
            $token
        );
    }

    /**
     * Check if token is an inline markup start token.
     *
     * For a user readable list of the following rules, see:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return boolean
     */
    protected function isInlineStartToken( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        return ( // Rule 1
             ( ( !isset( $this->documentStack[0] ) ) ||
               ( ( $this->documentStack[0]->token->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                 ( strpos( '\'"([{<-/:_', $this->documentStack[0]->token->content[0] ) !== false ) ) ||
               ( $this->documentStack[0]->token->type === ezcDocumentRstToken::WHITESPACE ) ||
               ( $token->position <= ( $this->indentation + 1 ) ) ) &&
             // Rule 2
             ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE ) &&
             ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) &&
             // Rule 5
             ( ( !isset( $this->documentStack[0] ) ) ||
               ( ( ( $this->documentStack[0]->token->content !== '"' ) || ( $tokens[0]->content !== '"' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '\'' ) || ( $tokens[0]->content !== '\'' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '(' ) || ( $tokens[0]->content !== ')' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '[' ) || ( $tokens[0]->content !== ']' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '{' ) || ( $tokens[0]->content !== '}' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '<' ) || ( $tokens[0]->content !== '>' ) ) ) ) );
    }

    /**
     * Check if token is an inline markup end token.
     *
     * For a user readable list of the following rules, see:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return boolean
     */
    protected function isInlineEndToken( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        return ( // Rule 3
             ( isset( $this->documentStack[0] ) ) &&
             ( $this->documentStack[0]->token->type !== ezcDocumentRstToken::WHITESPACE ) &&
             ( $token->position > ( $this->indentation + 1 ) ) &&
             // Rule 4
             ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) ||
               ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
               ( strpos( '\'")]}>-/:.,;!?\\_', $tokens[0]->content[0] ) !== false ) ) &&
             // Rule 5
             ( ( !isset( $this->documentStack[0] ) ) ||
               ( ( ( $this->documentStack[0]->token->content !== '"' ) || ( $tokens[0]->content !== '"' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '\'' ) || ( $tokens[0]->content !== '\'' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '(' ) || ( $tokens[0]->content !== ')' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '[' ) || ( $tokens[0]->content !== ']' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '{' ) || ( $tokens[0]->content !== '}' ) ) &&
                 ( ( $this->documentStack[0]->token->content !== '<' ) || ( $tokens[0]->content !== '>' ) ) ) ) );
    }

    /**
     * Detect inline literal
     *
     * As defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-literals
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftInlineLiteral( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( $token->content !== '``' )
        {
            return false;
        }

        if ( $this->isInlineStartToken( $token, $tokens ) )
        {
            /* DEBUG
            echo "   -> Found inline literal.\n";
            // /DEBUG */

            $node = new ezcDocumentRstMarkupInlineLiteralNode( $token );

            // Scan all subsequent tokens until we reach the closing token
            /* DEBUG
            echo "   -> Inline literal tokens: ";
            // /DEBUG */
            $nodes = array();
            while ( ( $literalToken = $tokens->shift() ) &&
                    ( ( $literalToken->content !== '``' ) ||
                      // The common inline markup end check does not apply
                      // here, as whitespace is not required preceed the
                      // closing ``.
                      !( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) ||
                         ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
                         ( strpos( '\'")]}>-/:.,;!?\\_', $tokens[0]->content[0] ) !== false ) ) ) )
            {
                /* DEBUG
                echo ".";
                // /DEBUG */
                $nodes[] = new ezcDocumentRstLiteralNode( $literalToken );
            }
            /* DEBUG
            echo "\n";
            // /DEBUG */

            // We found a closing markup node.
            if ( $literalToken !== null )
            {
                /* DEBUG
                echo "   => Shift inline literal node.\n";
                // /DEBUG */
                $node->nodes = $nodes;
                return $node;
            }
            else
            {
                /* DEBUG
                echo "   => No closing mark detected.\n";
                // /DEBUG */

                // Put all tokens back on stack.
                $nodes = array_reverse( $nodes );
                foreach ( $nodes as $node )
                {
                    $tokens->unshift( $node->token );
                }

                return false;
            }
        }

        // In other preconditions this is no inline markup, but maybe just text.
        return false;
    }

    /**
     * Detect inline markup
     *
     * As defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftInlineMarkup( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        switch ( $token->content )
        {
            case '*':
                $class = 'ezcDocumentRstMarkupEmphasisNode';
                break;
            case '**':
                $class = 'ezcDocumentRstMarkupStrongEmphasisNode';
                break;
            case '|':
                $class = 'ezcDocumentRstMarkupSubstitutionNode';
                break;
            default:
                // The found group of special characters are no inline markup,
                // but maybe just text...
                return false;
        }

        /* DEBUG
        echo "   -> Class: $class\n";
        // /DEBUG */

        if ( $this->isInlineStartToken( $token, $tokens ) )
        {
            // Create a markup open tag
            /* DEBUG
            echo "   => Create opening tag: $class\n";
            // /DEBUG */
            return new $class( $token, true );
        }

        if ( $this->isInlineEndToken( $token, $tokens ) )
        {
            // Create a markup close tag
            /* DEBUG
            echo "   => Create closing tag: $class\n";
            // /DEBUG */
            return new $class( $token, false );
        }

        // - Rule 6 is implicitely given by the tokenizer.
        // - Rule 7 is ensured by the escaping rules, defined in the shiftBackslash method.

        // In other preconditions this is no inline markup, but maybe just text.
        return false;
    }

    /**
     * Try to shift a interpreted text role
     *
     * Text role shifting is only called directly from the
     * shiftInterpretedTextMarkup() method and tries to find the associated
     * role.
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return mixed
     */
    protected function shiftInterpretedTextRole( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        /* DEBUG
        echo "   -> Scan for a role\n";
        // /DEBUG */

        if ( ( ( $token->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
               ( $token->content !== ':' ) ||
               ( !isset( $tokens[0] ) ) ||
               ( $tokens[0]->type !== ezcDocumentRstToken::TEXT_LINE ) ) &&
             ( ( $token->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
               ( $token->content !== '`' ) ||
               ( !isset( $tokens[0] ) ) ||
               ( $tokens[0]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
               ( $tokens[0]->content !== ':' ) ) )
        {
            return false;
        }

        $behind     = false;
        $aggregated = array();
        if ( $token->content === '`' )
        {
            $behind = true;
            $token  = $aggregated[] = $tokens->shift();
        }

        $role = '';
        do {
            $roleNameToken = $aggregated[] = $tokens->shift();
            $role .= $roleNameToken->content;
        } while ( isset( $tokens[0] ) &&
                  ( ( $tokens[0]->type === ezcDocumentRstToken::TEXT_LINE ) ||
                    ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                      ( preg_match( '(^[._-]+$)', $tokens[0]->content ) ) ) ) );

        // Check if this is really a valid role, otherwise put all checked
        // tokens back on the stack and do not handle this as a interpreted
        // text role.
        if ( !preg_match( '(^[A-Za-z0-9._-]+$)', $role ) ||
             !isset( $tokens[0] ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $tokens[0]->content !== ':' ) ||
             ( !$behind &&
               ( !isset( $tokens[1] ) ||
                 ( $tokens[1]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
                 ( $tokens[1]->content !== '`' ) ) ) )
        {
            /* DEBUG
            echo "   -> Not a role - reverting.\n";
            // /DEBUG */
            $tokens->prepend( $aggregated );
            return false;
        }

        /* DEBUG
        echo "   -> Found role: $role\n";
        // /DEBUG */

        // Remove last colon from token list
        $tokens->shift();
        return $role;
    }

    /**
     * Detect interpreted text inline markup
     *
     * As defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#interpreted-text
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftInterpretedTextMarkup( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        $role = false;
        if ( ( $token->content !== '`' ) &&
             !( $role = $this->shiftInterpretedTextRole( $token, $tokens ) ) )
        {
            return false;
        }

        // If we found a valid role before the token, update the current token
        // to the actual interpreted text markup start token
        if ( $role !== false )
        {
            $token = $tokens->shift();
        }

        /* DEBUG
        echo "   -> Interpreted text markup found, role: $role\n";
        // /DEBUG */

        if ( $this->isInlineEndToken( $token, $tokens ) )
        {
            // Create a markup close tag
            /* DEBUG
            echo "   => Create closing inline interpreted text tag.\n";
            // /DEBUG */
            $node = new ezcDocumentRstMarkupInterpretedTextNode( $token, false );
            $node->role = $this->shiftInterpretedTextRole( $token, $tokens );
            return $node;
        }

        if ( $this->isInlineStartToken( $token, $tokens ) )
        {
            // Create a markup open tag
            /* DEBUG
            echo "   => Create opening inline interpreted text tag.\n";
            // /DEBUG */
            $node = new ezcDocumentRstMarkupInterpretedTextNode( $token, true );
            $node->role = $role;
            return $node;
        }

        // - Rule 6 is implicitely given by the tokenizer.
        // - Rule 7 is ensured by the escaping rules, defined in the shiftBackslash method.

        // In other preconditions this is no inline markup, but maybe just text.
        return false;
    }

    /**
     * Detect inline markup
     *
     * As defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftAnonymousHyperlinks( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->content !== '__' ) ||
             ( $token->position === 1 ) )
        {
            // __ is the anonymous hyperlink token, skip all other cheks for
            // other special char tokens.
            return false;
        }

        // For a user readable list of the following rules, see:
        // http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
        //
        // For the anonymous hyperlink marker the same rules apply as for a
        // common end marker.
        if ( $this->isInlineEndToken( $token, $tokens ) )
        {
            // Create a markup close tag
            return new ezcDocumentRstAnonymousLinkNode( $token );
        }

        // In other preconditions this is no inline markup, but maybe just text.
        return false;
    }

    /**
     * Tries to detect footnote type
     *
     * The type of the footnote
     *
     * @param array $name
     * @return void
     */
    protected function detectFootnoteType( array $name )
    {
        $firstToken = reset( $name );
        switch ( $firstToken->content )
        {
            case '*':
                return ezcDocumentRstFootnoteNode::SYMBOL;

            case '#':
                return count( $name ) === 1 ?
                    ezcDocumentRstFootnoteNode::AUTO_NUMBERED :
                    ezcDocumentRstFootnoteNode::LABELED;

            default:
                return is_numeric( $firstToken->content ) ?
                    ezcDocumentRstFootnoteNode::NUMBERED :
                    ezcDocumentRstFootnoteNode::CITATION;
        }
    }

    /**
     * Detect reference
     *
     * As defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftReference( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( $token->content !== '_' )
        {
            // __ is the anonymous hyperlink token, skip all other cheks for
            // other special char tokens.
            return false;
        }

        // For a user readable list of the following rules, see:
        // http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
        //
        // For the anonymous hyperlink marker the same rules apply as for a
        // common end marker.
        if ( // Custom rule to detect citation and footnote references
             ( ( isset( $this->documentStack[0] ) ) &&
               ( $this->documentStack[0]->token->content === ']' ) &&
               ( $this->documentStack[0]->token->type === ezcDocumentRstToken::SPECIAL_CHARS ) ) &&
             // Rule 4
             ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) ||
               ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
               ( strpos( '\'")]}>-/:.,;!?\\', $tokens[0]->content[0] ) !== false ) ) )
        {
            // Create a markup close tag
            return new ezcDocumentRstReferenceNode( $token );
        }

        // In other preconditions this is no inline markup, but maybe just text.
        return false;
    }

    /**
     * Detect inline markup
     *
     * As defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftExternalReference( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( $token->content !== '_' )
        {
            // __ is the anonymous hyperlink token, skip all other cheks for
            // other special char tokens.
            return false;
        }

        // For a user readable list of the following rules, see:
        // http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
        //
        // For the anonymous hyperlink marker the same rules apply as for a
        // common end marker.
        if ( $this->isInlineEndToken( $token, $tokens ) )
        {
            // Create a markup close tag
            return new ezcDocumentRstExternalReferenceNode( $token );
        }

        // In other preconditions this is no inline markup, but maybe just text.
        return false;
    }

    /**
     * Blockquote annotations
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftBlockquoteAnnotation( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->content !== '--' ) &&
             ( $token->content !== '---' ) &&
             // Also the unicode character for EM-Dash is allowed
             ( $token->content !== "\x20\x14" ) )
        {
            // The special character group is not one of the allowed annotation markers
            return false;
        }

        if ( !isset( $this->documentStack[0] ) ||
             ( $this->documentStack[0]->type !== ezcDocumentRstNode::BLOCKQUOTE ) ||
             ( $this->indentation === 0 ) )
        {
            // Annotations only follow blockquotes.
            /* DEBUG
            echo "   -> Annotation not preceeded by blockquote.\n";
            // /DEBUG */
            return false;
        }

        // The section on blockquote annotations
        // http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#block-quotes
        // does not tell anything about the stuff, which may be used in there.
        // We assume everything is possible like in normal paragraphs. The text
        // is added during blockquote reduction.
        return new ezcDocumentRstBlockquoteAnnotationNode( $token );
    }

    /**
     * Is enumerated list?
     *
     * As defined at
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#bullet-lists
     *
     * Checks if the curretn token with thw following tokens may be an
     * enumerated list. Used by the repective shifting method and when checking
     * for indentation updates.
     *
     * Returns true, if the tokens may be an enumerated list, and false otherwise.
     *
     * @param ezcDocumentRstStack $tokens 
     * @param mixed $token 
     * @return void
     */
    protected function isEnumeratedList( ezcDocumentRstStack $tokens, $token = null )
    {
        $tokens = $tokens->asArray( 5 );
        if ( $token === null )
        {
            $token = array_shift( $tokens );
        }

        if ( $token === null )
        {
            return false;
        }

        // This pattern matches upper and lowercase roman numbers up 4999,
        // normal integers to any limit and alphabetic chracters.
        $enumeratedListPattern = '(^(?:(m{0,4}d?c{0,3}l?x{0,3}v{0,3}i{0,3}v?x?l?c?d?m?)|(M{0,4}D?C{0,3}L?X{0,3}V{0,3}I{0,3}V?X?L?C?D?M?)|([1-9]+[0-9]*)|([a-z])|([A-Z]))$)S';
        $matchOrderType = array(
            1 => ezcDocumentRstEnumeratedListNode::LOWER_ROMAN,
            2 => ezcDocumentRstEnumeratedListNode::UPPER_ROMAN,
            3 => ezcDocumentRstEnumeratedListNode::NUMERIC,
            4 => ezcDocumentRstEnumeratedListNode::LOWERCASE,
            5 => ezcDocumentRstEnumeratedListNode::UPPERCASE,
        );

        // Create enumerated list from list items surrounded by parantheses
        if ( ( $token->content === '(' ) &&
             isset( $tokens[1] ) &&
             ( $tokens[1]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
             ( $tokens[1]->content === ')' ) &&
             isset( $tokens[2] ) &&
             ( ( $tokens[2]->type === ezcDocumentRstToken::WHITESPACE ) ||
               ( $tokens[2]->type === ezcDocumentRstToken::NEWLINE ) ) &&
             isset( $tokens[0] ) &&
             ( ( ( $tokens[0]->type === ezcDocumentRstToken::TEXT_LINE ) &&
                 ( preg_match( $enumeratedListPattern, $tokens[0]->content, $match ) ) ) ||
               ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                 ( $tokens[0]->content === '#' ) ) ) )
        {
            /* DEBUG
            echo "   -> Found full framed enumeration list item.\n";
            // /DEBUG */
            foreach ( $matchOrderType as $number => $type )
            {
                if ( !empty( $match[$number] ) )
                {
                    return $type;
                }
            }

            return true;
        }

        // Create enumerated list from list items followed by a parantheses or
        // a dot
        if ( isset( $tokens[0] ) &&
             ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
             ( ( $tokens[0]->content === ')' ) ||
               ( $tokens[0]->content === '.' ) ) &&
             isset( $tokens[1] ) &&
             ( ( $tokens[1]->type === ezcDocumentRstToken::WHITESPACE ) ||
               ( $tokens[1]->type === ezcDocumentRstToken::NEWLINE ) ) &&
             ( ( ( $token->type === ezcDocumentRstToken::TEXT_LINE ) &&
                 ( preg_match( $enumeratedListPattern, $token->content, $match ) ) ) ||
               ( ( $token->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                 ( $token->content === '#' ) ) ) )
        {
            /* DEBUG
            echo "   -> Found half framed enumeration list item.\n";
            // /DEBUG */
            foreach ( $matchOrderType as $number => $type )
            {
                if ( !empty( $match[$number] ) )
                {
                    return $type;
                }
            }

            return true;
        }

        /* DEBUG
        echo "   -> Not an enumeration list item.\n";
        // /DEBUG */
        return false;
    }

    /**
     * Enumerated lists
     *
     * As defined at
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#bullet-lists
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftEnumeratedList( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        // The bullet list should always start at the very beginning of a line
        // / paragraph, so that the char postion should match the current
        // identation level.
        if ( $token->position !== ( $this->indentation + 1 ) )
        {
            /* DEBUG
            echo "   -> Indentation mismatch ({$token->position} <> {$this->indentation})\n";
            // /DEBUG */
            return false;
        }

        if ( !( $listType = $this->isEnumeratedList( $tokens, $token ) ) )
        {
            return false;
        }

        // We now know, that we have a bullet list, and can just shift the
        // tokens accordingly.

        // An opening brace is the only possible content before the actual list
        // item identifier - skip it.
        $content = '';
        $indentationIncrease = 1;
        if ( $token->content === '(' )
        {
            $token = $tokens->shift();
            $content .= $token->content;
            $indentationIncrease = 2;
        }

        // The bullet list should always start at the very beginning of a line
        // / paragraph, so that the char postion should match the current
        // identation level.
        if ( $token->position !== ( $this->indentation + $indentationIncrease ) )
        {
            /* DEBUG
            echo "   -> Indentation mismatch ({$token->position} <> {$this->indentation})\n";
            // /DEBUG */
            return false;
        }

        $text = $token;
        $char = $tokens->shift();

        // Only shift next token, if it is a whitespace - preserve newlines
        $whitespace = ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) ? $tokens->shift() : $tokens[0];

        /* DEBUG
        echo "   => Indentation updated to {$this->indentation}.\n";
        // /DEBUG */
        $this->indentation = $text->position + iconv_strlen( $text->content, 'UTF-8' ) +
            iconv_strlen( $whitespace->content, 'UTF-8' ) + iconv_strlen( $char->content, 'UTF-8' ) - 1;
        $node = new ezcDocumentRstEnumeratedListNode( $text );
        $node->text        = $content . $text->content . $char->content . $whitespace->content;
        $node->indentation = $this->indentation;
        $node->listType    = $listType;
        return $node;
    }

    /**
     * Bullet point lists
     *
     * As defined at
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#bullet-lists
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftBulletList( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        // Check if the special character group matches the known bullet list
        // starting characters.
        if ( !in_array( $token->content, array(
                '*', '-', '+',
                "\xe2\x80\xa2", "\xe2\x80\xa3", "\xe2\x81\x83"
            ) ) )
        {
            return false;
        }

        // The bullet list should always start at the very beginning of a line
        // / paragraph, so that the char postion should match the current
        // identation level.
        if ( $token->position !== ( $this->indentation + 1 ) )
        {
            /* DEBUG
            echo "   -> Indentation mismatch ({$token->position} <> {$this->indentation})\n";
            // /DEBUG */
            return false;
        } // /DEBUG */

        // The next token has to be a whitespace, which length also defines the
        // new indentation level.
        if ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE )
        {
            /* DEBUG
            echo "   -> No whitespace.\n";
            // /DEBUG */
            return false;
        }
        $whitespace = $tokens->shift();

        // Update indentation level
        $this->indentation = $token->position + iconv_strlen( $whitespace->content, 'UTF-8' );
        /* DEBUG
        echo "   => Indentation updated to {$this->indentation}.\n";
        // /DEBUG */

        // This seems to be a valid bullet list
        $node = new ezcDocumentRstBulletListNode( $token );
        $node->indentation = $this->indentation;
        return $node;
    }

    /**
     * Just keep text as text nodes
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTextLineNode
     */
    protected function shiftWhitespaceAsText( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        return new ezcDocumentRstTextLineNode(
            $token
        );
    }

    /**
     * Keep the newline as a single whitespace to maintain readability in
     * texts.
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTextLineNode
     */
    protected function shiftAsWhitespace( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( isset( $this->documentStack[0] ) &&
             ( in_array( $this->documentStack[0]->type, $this->textNodes ) ) )
        {
            /* DEBUG
            echo "    => Add additional whitespace to last node.\n";
            // /DEBUG */
            $token->content = ' ';
            return new ezcDocumentRstTextLineNode( $token );
        }

        return false;
    }

    /**
     * Just keep text as text nodes
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstTextLineNode
     */
    protected function shiftSpecialCharsAsText( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        return new ezcDocumentRstTextLineNode(
            $token
        );
    }

    /**
     * Shift literal block
     *
     * Shift a complete literal block into one node. The behaviour of literal
     * blocks is defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#literal-blocks
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftLiteralBlock( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->content !== '::' ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ||
             ( !isset( $tokens[1] ) ) ||
             ( $tokens[1]->type !== ezcDocumentRstToken::NEWLINE ) )
        {
            // Literal blocks only start by a double colon: '::', and has
            // always to be followed by two newlines (marking a common
            // paragraph).
            return false;
        }

        // Check if we should add a text node to the stack first, including a
        // single colon.
        if ( ( $token->position > $this->indentation ) &&
             ( isset( $this->documentStack[0] ) ) &&
             ( in_array( $this->documentStack[0]->type, $this->textNodes ) ) &&
             ( $this->documentStack[0]->token->type !== ezcDocumentRstToken::WHITESPACE ) )
        {
            $tokens->unshift(
                new ezcDocumentRstToken(
                    ezcDocumentRstToken::SPECIAL_CHARS, '::', $token->line, $this->indentation
                )
            );

            // Return a new text node first, the new pseudo-token starting the
            // literal block will be handled in the next iteration.
            /* DEBUG
            echo "  => Create new text node, handle the literal block later.\n";
            // /DEBUG */
            return new ezcDocumentRstTextLineNode(
                new ezcDocumentRstToken(
                    ezcDocumentRstToken::TEXT_LINE, ':', $token->line, $token->position, true
                )
            );
        }

        // If the token is onyl preceeded by a textnode we put it back on the
        // token stack an return a paragraph node first, to close the previous
        // paragraph. In the next iteration the literal block will be handled.
        if ( isset( $this->documentStack[0] ) &&
             in_array( $this->documentStack[0]->type, $this->textNodes ) )
        {
            $tokens->unshift( $token );
            /* DEBUG
            echo "  => Create a paragraph for the preceeding text stuff first.\n";
            // /DEBUG */
            return new ezcDocumentRstParagraphNode( $token );
        }

        // Skip all empty lines first
        while ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE )
        {
            /* DEBUG
            echo "  -> Skip newline.\n";
            // /DEBUG */
            $tokens->shift();
        }

        // Once we got the first line after the literal block start marker, we
        // check for the quoting style
        if ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE )
        {
            // In case of a whitespace indentation token, this is used
            // completely as indentation marker.
            /* DEBUG
            echo "  -> Detected whitespace indetation..\n";
            // /DEBUG */
            $baseIndetation = clone $tokens[0];
        }
        elseif ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS )
        {
            // In case of special characters we expect each line to start with
            // the same single character, while the original content is
            // preserved.
            /* DEBUG
            echo "  -> Detected special character indetation..\n";
            // /DEBUG */
            $baseIndetation = new ezcDocumentRstToken(
                ezcDocumentRstToken::SPECIAL_CHARS,
                $tokens[0]->content[0], $tokens[0]->line, $tokens[0]->position
            );
        }
        else
        {
            // In other case we got something unexpected.
            return false;
        }

        $collected = array();
        $minIndentation = iconv_strlen( $baseIndetation->content, 'UTF-8' );
        while ( // Empty lines are inlcuded.
                ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
                // All other lines must start with the determined base
                // indentation
                ( ( $tokens[0]->type === $baseIndetation->type ) &&
                  ( ( strpos( $tokens[0]->content, $baseIndetation->content ) === 0 ) ||
                    ( ( $baseIndetation->type === ezcDocumentRstToken::WHITESPACE ) &&
                      ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) > $this->indentation ) ) ) ) )
        {
            $literalToken = $tokens->shift();
            if ( $literalToken->type === ezcDocumentRstToken::NEWLINE )
            {
                // Nothing to do for empty lines, but they are included in the
                // literal block.
                /* DEBUG
                echo "  -> Collected plain newline.\n";
                // /DEBUG */
                $collected[] = new ezcDocumentRstLiteralNode( $literalToken );
                continue;
            }

            if ( $baseIndetation->type === ezcDocumentRstToken::WHITESPACE )
            {
                // Remove whitespaces used for indentation in literal blocks
                $minIndentation = min( iconv_strlen( $literalToken->content, 'UTF-8' ), $minIndentation );
                /* DEBUG
                echo "  -> Minimum indentation: $minIndentation.\n";
                // /DEBUG */
            }

            $collected[] = new ezcDocumentRstLiteralNode( $literalToken );

            // Just collect everything until we reach a newline, the the check
            // starts again.
            /* DEBUG
            echo "  -> Collect: ";
            // /DEBUG */
            do {
                /* DEBUG
                echo ".";
                // /DEBUG */
                $collected[] = new ezcDocumentRstLiteralNode( $item = $tokens->shift() );
            }
            while ( $item->type !== ezcDocumentRstToken::NEWLINE );
            /* DEBUG
            echo "\n";
            // /DEBUG */
        }
        /* DEBUG
        echo "  => Finished collecting.\n";
        // /DEBUG */

        // Readd the last newline to the token stack
        $tokens->unshift( $item );

        // When literal block is indented by whitespace, remove the minimum
        // indentation. from each line.
        if ( $baseIndetation->type === ezcDocumentRstToken::WHITESPACE )
        {
            foreach ( $collected as $node )
            {
                if ( ( $node->token->position <= 1 ) &&
                     ( $node->token->type === ezcDocumentRstToken::WHITESPACE ) )
                {
                    $node->token->content = substr( $node->token->content, $minIndentation );
                }
            }
        }

        // Nothing more could be collected, either because the indentation has
        // been reduced, or the markers are missing. Create the literal block
        // node.
        $node = new ezcDocumentRstLiteralBlockNode( $token, $collected );
        $node->indentation = $this->indentation;
        return $node;
    }

    /**
     * Read all token until one of the given tokens occurs
     *
     * Reads all tokens and removes them from the token stack, which do not
     * match of the given tokens. Escaping is maintained.
     *
     * @param ezcDocumentRstStack $tokens
     * @param array $until
     * @return array
     */
    protected function readUntil( ezcDocumentRstStack $tokens, array $until )
    {
        $quoted = false;
        if ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
             ( $tokens[0]->content === '`' ) )
        {
            $tokens->shift();
            $until = array( array(
                'type'    => ezcDocumentRstToken::SPECIAL_CHARS,
                'content' => '`',
            ) );
            $quoted = true;
        }

        $foundTokens = array();
        $found = false;
        do {
            if ( $tokens[0]->type === ezcDocumentRstToken::BACKSLASH )
            {
                $backslash = $tokens->shift();
                $this->shiftBackslash( $backslash, $tokens );
            }

            foreach ( $until as $check )
            {
                if ( ( !isset( $check['type'] ) ||
                       ( $tokens[0]->type === $check['type'] ) ) &&
                     ( !isset( $check['content'] ) ||
                       ( $tokens[0]->content === $check['content'] ) ) )
                {
                    $found = true;
                    break 2;
                }
            }

            $foundTokens[] = $tokens->shift();
        } while ( $found === false );

        if ( $quoted )
        {
            $tokens->shift();
        }

        return $foundTokens;
    }

    /**
     * Read multiple lines
     *
     * Reads the content of multiple indented lines, where the indentation can
     * bei either handled strict, or lose, when literal text is expected.
     *
     * Returns an array with the collected tokens, until the indentation
     * changes.
     *
     * @param ezcDocumentRstStack $tokens
     * @param bool $strict
     * @return array
     */
    protected function readMutlipleIndentedLines( ezcDocumentRstStack $tokens, $strict = false )
    {
        /* DEBUG
        echo "  -> Read follow up text.\n";
        // /DEBUG */
        $collected = array();
        if ( $tokens[0]->position > ( $this->indentation + 1 ) )
        {
            // The first token is a follow up token to something before. We
            // ignore the indentation here, and read everything in the first
            // line.
            do {
                $collected[] = $token = $tokens->shift();
            } while ( $token->type !== ezcDocumentRstToken::NEWLINE );
        }
        /* DEBUG
        echo "  -> Found " . count( $collected ) . " tokens in same line.\n";
        // /DEBUG */

        // Now check for the actual indentation and aggregate everything which
        // stays indented like this.
        if ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
             ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) > $this->indentation ) )
        {
            /* DEBUG
            echo "  -> Whitespace indentation.\n";
            // /DEBUG */
            $indentation = clone $tokens[0];
        }
        else
        {
            // We require indenteation here, so return, when the follow up text
            // is not indented at all.
            /* DEBUG
            echo "  -> No indented text.\n";
            // /DEBUG */
            return $collected;
        }

        $minIndentation = iconv_strlen( $indentation->content, 'UTF-8' );
        while ( ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
                ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
                  ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) > $this->indentation ) ) )
        {
            if ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE )
            {
                $collected[] = $token = $tokens->shift();
                // Just skip empty lines
                /* DEBUG
                echo "  -> Skip empty line.\n";
                // /DEBUG */
                continue;
            }

            // Update minimum indentation
            $whitespace = $tokens->shift();
            $minIndentation = min( iconv_strlen( $whitespace->content, 'UTF-8' ), $minIndentation );

            // Read all further nodes until the next newline, and check for
            // indentation again then.
            /* DEBUG
            echo "  -> Collect: ";
            // /DEBUG */
            do {
                /* DEBUG
                echo ".";
                // /DEBUG */
                $collected[] = $token = $tokens->shift();
            } while ( $token->type !== ezcDocumentRstToken::NEWLINE );
            /* DEBUG
            echo "\n";
            // /DEBUG */
        }

        // Add last to to stack again, is useful for common reduction handling.
        $tokens->unshift( $token );

        // Remove minimum found indentation from indentation tokens.
        foreach ( $collected as $token )
        {
            if ( $token->position <= 1 )
            {
                $token->content = substr( $token->content, $minIndentation );
            }
        }
        /* DEBUG
        echo "  => Collected " . count( $collected ) . " tokens.\n";
        // /DEBUG */
        return $collected;
    }

    /**
     * Shift directives
     *
     * Shift directives as a subaction of the shiftComment method, though the
     * signature differs from the common shift methods.
     *
     * This method aggregated options and parameters of directives, but leaves
     * the content aggregation to the common comment aggregation.
     *
     * @param ezcDocumentRstDirectiveNode $directive
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstDirectiveNode
     */
    protected function shiftDirective( ezcDocumentRstDirectiveNode $directive, ezcDocumentRstStack $tokens )
    {
        // All nodes until the first newline are the so called parameters of
        // the directive.
        $parameters      = '';
        $directiveTokens = array();
        while ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE )
        {
            $token             = $tokens->shift();
            $parameters       .= $token->content;
            $directiveTokens[] = $token;
        }
        /* DEBUG
        echo "  -> Set directive parameter: $parameters\n";
        // /DEBUG */
        $directive->parameters = $parameters;
        $directive->tokens     = $directiveTokens;
        $tokens->shift();

        // If there are two newlines, there are no options.
        if ( $tokens[0]->type == ezcDocumentRstToken::NEWLINE )
        {
            return $directive;
        }

        // After that there may be options, which are indented and start with a
        // colon.
        while ( isset( $tokens[0] ) &&
                ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
                isset( $tokens[1] ) &&
                ( $tokens[1]->content === ':' ) &&
                isset( $tokens[3] ) &&
                ( $tokens[3]->content === ':' ) )
        {
            $tokens->shift();
            $tokens->shift();

            // Extract option name
            $name = '';
            while ( isset( $tokens[0] ) &&
                    $tokens[0]->content !== ':' )
            {
                $token = $tokens->shift();
                $name .= $token->content;
            }
            $tokens->shift();

            // Extract option value
            $value = '';
            while ( isset( $tokens[0] ) &&
                    ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) )
            {
                $token = $tokens->shift();
                $value .= $token->content;
            }

            // Assign option on directive
            $tokens->shift();
            $directive->options[$name] = $value;
            /* DEBUG
            echo "  -> Set directive option: $name => $value\n";
            // /DEBUG */
        }

        // Leave everything else up to the comment shifter
        return $directive;
    }

    /**
     * Handle special directives
     *
     * Handle special directives like replace, which require reparsing of the
     * directives contents, which is not possible to do during visiting, but is
     * required to already be done inside the parser.
     *
     * @param ezcDocumentRstSubstitutionNode $substitution
     * @param ezcDocumentRstDirectiveNode $node
     * @return ezcDocumentRstSubstitutionNode
     */
    protected function handleSpecialDirectives( ezcDocumentRstSubstitutionNode $substitution, ezcDocumentRstDirectiveNode $node )
    {
        // Special handling for some directives
        switch ( strtolower( $node->identifier ) )
        {
            case 'replace':
                // Reenter the parser with the replace directive contents
                // and assign the contents from the first paragraph to the
                // substitution target node.
                if ( !count( $node->tokens ) )
                {
                    $substitution->nodes = array();
                    break;
                }

                $document = $this->reenterParser( $node->tokens );
                $contents = array();
                foreach ( $document->nodes as $section )
                {
                    $contents = array_merge( $contents, $section->nodes );
                }
                $substitution->nodes = $contents;
                break;
            // The date and unicode directives would be required to be
            // handled similarly.
        }

        return $substitution;
    }

    /**
     * Shift comment
     *
     * Shift comments. Comments are introduced by '..' and just contain text.
     * There are several other block, which are introduced the same way, but
     * where the first token determines the actual type.
     *
     * This method implements the parsing and detection of those different
     * items.
     *
     * Comments are basically described here, but there are crosscutting
     * concerns throughout the complete specification:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#comments
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftComment( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->content !== '..' ) ||
             ( $token->position > ( $this->indentation + 1 ) ) ||
             ( !isset( $tokens[0] ) ) ||
             ( ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE ) &&
               ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ) ||
             ( isset( $tokens[1] ) &&
               ( $tokens[1]->type === ezcDocumentRstToken::NEWLINE ) ) )
        {
            // All types handled by this method are introduced by a token
            // containing '..' at the very beginning of the line, followed by a
            // whitespace.
            return false;
        }

        // Ignore the following whitespace
        $tokens->shift();

        // The next tokens determine which type of structure we found, while
        // everything which is not handled by a special case falls back to a
        // comment.
        $determined = false;
        $substitution = null;
        while ( !$determined )
        {
            switch ( true )
            {
                case $tokens[0]->type === ezcDocumentRstToken::TEXT_LINE:
                    // We may have found a directive. Aggregate the identifier and
                    // check for two colons after that.
                    $identifierTokens = array();
                    $identifier = '';
                    /* DEBUG
                    echo "  -> This may be a directive...\n";
                    // /DEBUG */
                    while ( ( $tokens[0]->type === ezcDocumentRstToken::TEXT_LINE ) ||
                            ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                              ( in_array( $tokens[0]->content[0], array( '-', '_', '.' ) ) ) ) )
                    {
                        $identifierTokens[] = $iToken = $tokens->shift();
                        $identifier .= $iToken->content;
                    }

                    // Right after the identifier there should be a double colon,
                    // otherwise this is just a plain comment.
                    if ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                         ( $tokens[0]->content === '::' ) )
                    {
                        /* DEBUG
                        echo "  -> Found directive.\n";
                        // /DEBUG */
                        $tokens->shift();
                        $node = new ezcDocumentRstDirectiveNode( $token, strtolower( trim( $identifier ) ) );
                        // The shiftDirective method aggregates options and
                        // parameters of the directive and the contents will be
                        // aggregated later by the common comment functionality.
                        $this->shiftDirective( $node, $tokens );
                        $determined = true;
                    }
                    else
                    {
                        /* DEBUG
                        echo "  -> Just a comment.\n";
                        // /DEBUG */
                        // We create a comment node, where all following contents
                        // may also be aggregated.
                        $node = new ezcDocumentRstCommentNode( $token );
                        $determined = true;

                        // Add tokens used for type detection to the begin of
                        // the comment node.
                        foreach ( $identifierTokens as $subtoken )
                        {
                            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
                        }
                    }
                    break;

                case ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                       ( $tokens[0]->content === '|' ) ):
                    /* DEBUG
                    echo "  -> Found a substitution target.\n";
                    // /DEBUG */
                    // We found a substitution directive. It is identified by the
                    // text between the pipes and the reenters this parsing
                    // process.
                    $name = array_merge(
                        array( $tokens->shift() ),
                        $this->readUntil( $tokens, array(
                            array(
                                'type' => ezcDocumentRstToken::NEWLINE,
                            ),
                            array(
                                'type' => ezcDocumentRstToken::SPECIAL_CHARS,
                                'content' => '|',
                            ),
                        ) )
                    );

                    // Right after the identifier there should be a double colon,
                    // otherwise this is just a plain comment.
                    if ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                         ( $tokens[0]->content === '|' ) )
                    {
                        $name[] = $tokens->shift();
                        /* DEBUG
                        echo "  -> Substitution target successfully found.\n";
                        // /DEBUG */
                        $substitution = new ezcDocumentRstSubstitutionNode( $token, array_slice( $name, 1, -1 ) );
                        // After we found a substitution directive, we reenter
                        // the process to find a associated directive.

                        // Skip following whitespace
                        if ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE )
                        {
                            $tokens->shift();
                        }
                    }
                    else
                    {
                        /* DEBUG
                        echo "  -> Just a comment.\n";
                        // /DEBUG */
                        // We create a comment node, where all following contents
                        // may also be aggregated.
                        $node = new ezcDocumentRstCommentNode( $token );
                        $determined = true;

                        // Add tokens used for type detection to the begin of
                        // the comment node.
                        foreach ( $name as $subtoken )
                        {
                            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
                        }
                    }
                    break;

                case ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                       ( $tokens[0]->content === '[' ) ):
                    /* DEBUG
                    echo "  -> Found a potential footnote target.\n";
                    // /DEBUG */
                    // We found a substitution directive. It is identified by the
                    // text between the pipes and the reenters this parsing
                    // process.
                    $name = array_merge(
                        array( $tokens->shift() ),
                        $this->readUntil( $tokens, array(
                            array(
                                'type' => ezcDocumentRstToken::NEWLINE,
                            ),
                            array(
                                'type' => ezcDocumentRstToken::SPECIAL_CHARS,
                                'content' => ']',
                            ),
                        ) )
                    );

                    // Right after the identifier there should be a double colon,
                    // otherwise this is just a plain comment.
                    if ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                         ( $tokens[0]->content === ']' ) )
                    {
                        $name[] = $tokens->shift();
                        /* DEBUG
                        echo "  -> Footnote target successfully found.\n";
                        // /DEBUG */
                        $node = new ezcDocumentRstFootnoteNode( $token, $name = array_slice( $name, 1, -1 ), $this->detectFootnotetype( $name ) );
                        // With the name we find the associated contents, which
                        // may span multiple lines, so that this is done by a
                        // seperate method.
                        $content = $this->readMutlipleIndentedLines( $tokens, true );
                        $section = $this->reenterParser( $content );
                        $node->nodes = $section->nodes;

                        // There is nothing more to read. We can exit immediately.
                        return $node;
                    }
                    else
                    {
                        /* DEBUG
                        echo "  -> Just a comment.\n";
                        // /DEBUG */
                        // We create a comment node, where all following contents
                        // may also be aggregated.
                        $node = new ezcDocumentRstCommentNode( $token );
                        $determined = true;

                        // Add tokens used for type detection to the begin of
                        // the comment node.
                        foreach ( $name as $subtoken )
                        {
                            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
                        }
                    }
                    break;

                case ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                       ( $tokens[0]->content === '_' ) ):
                    /* DEBUG
                    echo "  -> Found a named reference target.\n";
                    // /DEBUG */
                    // We found a named reference target. It is identified by a
                    // starting underscrore, followed by the reference name,
                    // and the reference target.
                    $tokens->shift();
                    $name = $this->readUntil( $tokens, array(
                        array(
                            'type' => ezcDocumentRstToken::NEWLINE,
                        ),
                        array(
                            'type' => ezcDocumentRstToken::SPECIAL_CHARS,
                            'content' => ':',
                        ),
                    ) );

                    // Right after the identifier there should be a double colon,
                    // otherwise this is just a plain comment.
                    if ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                         ( $tokens[0]->content === ':' ) )
                    {
                        $tokens->shift();
                        $tokens->shift();
                        /* DEBUG
                        echo "  -> Named reference target successfully found.\n";
                        // /DEBUG */
                        $node = new ezcDocumentRstNamedReferenceNode( $token, $name );
                        // With the name we find the associated contents, which
                        // may span multiple lines, so that this is done by a
                        // seperate method.
                        $content = $this->readMutlipleIndentedLines( $tokens, true );
                        foreach ( $content as $subtoken )
                        {
                            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
                        }

                        // There is nothing more to read. We can exit immediately.
                        return $node;
                    }
                    else
                    {
                        /* DEBUG
                        echo "  -> Just a comment.\n";
                        // /DEBUG */
                        // We create a comment node, where all following contents
                        // may also be aggregated.
                        $node = new ezcDocumentRstCommentNode( $token );
                        $determined = true;

                        // Add tokens used for type detection to the begin of
                        // the comment node.
                        foreach ( $name as $subtoken )
                        {
                            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
                        }
                    }
                    break;

                case ( ( $tokens[0]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                       ( $tokens[0]->content === '__' ) &&
                       ( isset( $tokens[1] ) ) &&
                       ( $tokens[1]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                       ( $tokens[1]->content === ':' ) ):
                    /* DEBUG
                    echo "  -> Found a anonymous reference target.\n";
                    // /DEBUG */
                    // We found a anonymous reference target. It is identified
                    // by two starting underscrores, directly followed by a
                    // colon.
                    $tokens->shift();
                    $tokens->shift();
                    $tokens->shift();

                    $node = new ezcDocumentRstAnonymousReferenceNode( $token );
                    // With the name we find the associated contents, which
                    // may span multiple lines, so that this is done by a
                    // seperate method.
                    $content = $this->readMutlipleIndentedLines( $tokens, true );
                    foreach ( $content as $subtoken )
                    {
                        $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
                    }

                    // There is nothing more to read. We can exit immediately.
                    return $node;

                default:
                    // Everything else starting with '..' is just a comment.
                    /* DEBUG
                    echo "  -> Found comment.\n";
                    // /DEBUG */
                    $node = new ezcDocumentRstCommentNode( $token );
                    $determined = true;
                    break;
            }
        }

        // Set current indentation on returned node
        $node->indentation = $this->indentation;

        // If this is part of a substitution reference, we return the
        // substitution after the process and not just the plain node.
        if ( $substitution !== null )
        {
            $substitution->nodes = array( $node );
            $return = $substitution;
        }
        else
        {
            $return = $node;
        }

        // Check if this is a short directive - in this case we skip the
        // following aggregation and return the directive directly.
        if ( ( ( $node instanceof ezcDocumentRstDirectiveNode ) &&
               ( in_array( $node->identifier, $this->shortDirectives, true ) ) ) ||
             ( !count( $tokens ) ) )
        {
            // Handle special cases like the replace directive
            if ( $substitution !== null )
            {
                $return = $this->handleSpecialDirectives( $substitution, $node );
            }

            return $return;
        }

        // Skip all empty lines first
        $skippedLine = null;
        while ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE )
        {
            /* DEBUG
            echo "  -> Skip newline.\n";
            // /DEBUG */
            $skippedLine = $tokens->shift();
        }

        // Once we got the first line after the literal block start marker, we
        // check for the quoting style
        if ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
             ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) > $this->indentation ) )
        {
            // In case of a whitespace indentation token, this is used
            // completely as indentation marker.
            /* DEBUG
            echo "  -> Detected whitespace indetation..\n";
            // /DEBUG */
            $baseIndetation = clone $tokens[0];
        }
        else
        {
            // If no qouting could be detected, we are finished now, and the
            // comment / directive / ... has no more content.

            // Readd the last newline to the token stack
            if ( $skippedLine !== null )
            {
                $tokens->unshift( $skippedLine );
            }

            // Handle special cases like the replace directive
            if ( $substitution !== null )
            {
                $return = $this->handleSpecialDirectives( $substitution, $node );
            }

            return $return;
        }

        // Collect all contents, until the indentation changes.
        $collected       = array();
        $directiveTokens = array();
        while ( // Empty lines are inlcuded.
                ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
                // All other lines must start with the determined base
                // indentation
                ( ( $tokens[0]->type === $baseIndetation->type ) &&
                  ( ( strpos( $tokens[0]->content, $baseIndetation->content ) === 0 ) ||
                    ( ( $baseIndetation->type === ezcDocumentRstToken::WHITESPACE ) &&
                      ( iconv_strlen( $tokens[0]->content, 'UTF-8' ) > $this->indentation ) ) ) ) )
        {
            $literalToken = $tokens->shift();
            if ( $literalToken->type === ezcDocumentRstToken::NEWLINE )
            {
                // Nothing to do for empty lines, but they are included in the
                // literal block.
                /* DEBUG
                echo "  -> Collected plain newline.\n";
                // /DEBUG */
                $collected[]       = new ezcDocumentRstLiteralNode( $literalToken );
                $directiveTokens[] = $literalToken;
                continue;
            }

            // Remove whitespaces used for indentation in literal blocks
            /* DEBUG
            echo "  -> Handle whitespace indentation.\n";
            // /DEBUG */
            $directiveTokens[]     = clone $literalToken;
            $literalToken->content = substr( $literalToken->content, strlen( $baseIndetation->content ) );
            $collected[]           = new ezcDocumentRstLiteralNode( $literalToken );

            // Just collect everything until we reach a newline, the the check
            // starts again.
            /* DEBUG
            echo "  -> Collect: ";
            // /DEBUG */
            do {
                /* DEBUG
                echo ".";
                // /DEBUG */
                $collected[]       = new ezcDocumentRstLiteralNode( $item = $tokens->shift() );
                $directiveTokens[] = $item;
            }
            while ( $item->type !== ezcDocumentRstToken::NEWLINE );
            /* DEBUG
            echo "\n";
            // /DEBUG */
        }
        /* DEBUG
        echo "  => Finished collecting.\n";
        // /DEBUG */

        // Readd the last newline to the token stack
        $tokens->unshift( $item );

        // Nothing more could be collected, either because the indentation has
        // been reduced, or the markers are missing. Add the aggregated
        // contents to the node and return it.
        $node->nodes = array_merge(
            $node->nodes,
            $collected
        );

        // For special directives like the replace directive we also need to
        // aggregate all found tokens on the tokens property
        if ( $node instanceof ezcDocumentRstDirectiveNode )
        {
            $node->tokens = array_merge(
                $node->tokens,
                $directiveTokens
            );

            if ( $node->identifier !== 'replace' )
            {
                $node->tokens = $this->realignTokens( $node->tokens );
            }
        }

        // Handle special cases like the replace directive
        if ( $substitution !== null )
        {
            $return = $this->handleSpecialDirectives( $substitution, $node );
        }

        return $return;
    }

    /**
     * Shift anonymous reference target
     *
     * Shift the short version of anonymous reference targets, the long version
     * is handled in the shiftComment() method. Both are specified at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#anonymous-hyperlinks
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftAnonymousReference( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->content !== '__' ) ||
             ( $token->position !== 1 ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE ) )
        {
            // This does not fulfill the requirements for a short anonymous
            // hyperling reference.
            return false;
        }

        // Shift whitespace
        $tokens->shift();

        $node = new ezcDocumentRstAnonymousReferenceNode( $token );
        // With the name we find the associated contents, which
        // may span multiple lines, so that this is done by a
        // seperate method.
        $content = $this->readMutlipleIndentedLines( $tokens, true );
        foreach ( $content as $subtoken )
        {
            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
        }

        // There is nothing more to read. We can exit immediately.
        return $node;
    }

    /**
     * Shift field lists
     *
     * Shift field lists, which are introduced by a term surrounded by columns
     * and any text following. Field lists are specified at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#field-lists
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftFieldList( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->content !== ':' ) ||
             ( $token->position > 1 ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) ||
             ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
             ( isset( $this->documentStack[0] ) &&
               ( in_array( $this->documentStack[0]->type, $this->textNodes ) ) ) )
        {
            // All types handled by this method are introduced by a token
            // containing ':' at the very beginning of the line, followed by
            // text.
            return false;
        }

        $name = $this->readUntil( $tokens, array(
                array(
                    'type' => ezcDocumentRstToken::NEWLINE,
                ),
                array(
                    'type' => ezcDocumentRstToken::SPECIAL_CHARS,
                    'content' => ':',
                ),
            )
        );

        if ( ( $tokens[0]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $tokens[0]->content !== ':' ) )
        {
            // Check that the read read stopped at the field list name end
            // marker, otherwise this is just some random text, at least no
            // valid field list.
            $tokens->prepend( $name );
            return false;
        }

        // Ignore the closing ':'.
        $tokens->shift();

        // Skip all empty lines before text starts
        while ( ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) ||
                ( ( $tokens[0]->type === ezcDocumentRstToken::WHITESPACE ) &&
                  ( $tokens[1]->type === ezcDocumentRstToken::NEWLINE ) ) )
        {
            $tokens->shift();
        }

        // Read all text, following the field list name
        $node = new ezcDocumentRstFieldListNode( $token, $name );
        // With the name we find the associated contents, which
        // may span multiple lines, so that this is done by a
        // seperate method.
        $content = $this->readMutlipleIndentedLines( $tokens, true );
        foreach ( $content as $subtoken )
        {
            $node->nodes[] = new ezcDocumentRstLiteralNode( $subtoken );
        }

        // There is nothing more to read. We can exit immediately.
        return $node;
    }

    /**
     * Read simple cells
     *
     * Read cells as defined in simple tables. Cells are maily structured by
     * whitespaces, but may also exceed one cell.
     *
     * Returns an array with cells, ordered by their rows and columns.
     *
     * @param array $cellStarts
     * @param ezcDocumentRstStack $tokens
     * @return array
     */
    protected function readSimpleCells( $cellStarts, &$tokens )
    {
        /* DEBUG
        echo "  -> Read simple table cells.";
        // /DEBUG */
        // Two dimensiponal structure with the actual cell contents.
        $cellContents = array();
        $row = -1;
        // Read until we got some kind of definition line.
        while ( ( ( !isset( $tokens[0] ) ) ||
                  ( $tokens[0]->position > 1 ) ||
                  ( $tokens[0]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
                  ( ( $tokens[0]->content[0] !== '=' ) &&
                    ( $tokens[0]->content[0] !== '-' ) ) ||
                  ( !isset( $tokens[1] ) ) ||
                  ( ( $tokens[1]->type !== ezcDocumentRstToken::WHITESPACE ) &&
                    ( $tokens[1]->type !== ezcDocumentRstToken::NEWLINE ) ) ) &&
                ( $token = $tokens->shift() ) )
        {
            // Increase row number, if the we get non-whitespace content in the
            // first cell
            if ( ( ( $row === -1 ) &&
                   ( $token->position === 1 ) ) ||
                 ( ( $row !== -1 ) &&
                   ( $token->position === 1 ) &&
                   ( $token->type !== ezcDocumentRstToken::WHITESPACE ) &&
                   ( $token->type !== ezcDocumentRstToken::NEWLINE ) ) )
            {
                ++$row;
                $column = false;
                /* DEBUG
                echo "\n   -> Row $row: ";
                // /DEBUG */
            }

            // Determine column for current content.
            foreach ( $cellStarts as $nr => $position )
            {
                if ( $position == $token->position )
                {
                    $column = $nr;
                    /* DEBUG
                    echo "Increase: $column, ";
                    // /DEBUG */
                    break;
                }
            }

            if ( $column === false )
            {
                $column = $nr;
                /* DEBUG
                echo "Init: $column, ";
                // /DEBUG */
            }

            // Check if we need to split up the token, because of a single
            // seperating whitespace at the column boundings.
            if ( isset( $cellStarts[$column + 1] ) &&
                 ( isset( $token->content[$split = $cellStarts[$column + 1] - $token->position - 1] ) ) &&
                 ( strlen( $token->content ) > ( $split + 1 ) ) &&
                 ( $token->content[$split] === ' ' ) )
            {
                /* DEBUG
                echo "Split, ";
                // /DEBUG */
                $newToken = clone( $token );
                $token->content = substr( $token->content, 0, $split );

                $newToken->content = substr( $newToken->content, $split + 1 );
                $newToken->position = $newToken->position + $split + 1;
                $tokens->unshift( $newToken );
            }

            // Append contents to column
            $cellContents[$row][$column][] = $token;
        }
        /* DEBUG
        echo "\n";
        // /DEBUG */

        return $cellContents;
    }

    /**
     * Read simple table specifications
     *
     * Read the column specification headers of a simple table and return the
     * sizes of the specified columns in an array.
     *
     * @param ezcDocumentRstStack $tokens
     * @return array
     */
    protected function readSimpleTableSpecification( &$tokens )
    {
        // Detect the cell sizes inside of the simple table.
        $tableSpec = array();
        /* DEBUG
        echo "  -> Table specification: ";
        // /DEBUG */
        while ( isset( $tokens[0] ) &&
                ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) )
        {
            $specToken = $tokens->shift();
            if ( ( ( $specToken->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                   ( ( $specToken->content[0] === '=' ) ||
                     ( $specToken->content[0] === '-' ) ) ) ||
                 ( ( $specToken->type === ezcDocumentRstToken::WHITESPACE ) &&
                   ( iconv_strlen( $specToken->content, 'UTF-8' ) >= 1 ) ) )
            {
                $tableSpec[] = array( $specToken->type, strlen( $specToken->content ) );
                /* DEBUG
                echo strlen( $specToken->content ), ", ";
                // /DEBUG */
            }
            else
            {
                $this->triggerError(
                    E_PARSE,
                    'Invalid token in simple table specifaction.',
                    null, $specToken->line, $specToken->position
                );
            }
        }
        $tokens->shift();
        /* DEBUG
        echo "\n";
        // /DEBUG */

        return $tableSpec;
    }

    /**
     * Shift simple table
     *
     * "Simple tables" are not defined by a complete grid, but only by top and
     * bottome lines. There exact specification can be found at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#simple-tables
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftSimpleTable( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->position > 1 ) ||
             ( $token->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $token->content[0] !== '=' ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::WHITESPACE ) ||
             ( !isset( $tokens[1] ) ) ||
             ( $tokens[1]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $tokens[1]->content[0] !== '=' ) )
        {
            // Missing multiple special character groups only containing '=',
            // separated by whitespaces, which introduce a simple table.
            return false;
        }

        /* DEBUG
        echo "  -> Found simple table.\n";
        // /DEBUG */
        // Detect the cell sizes inside of the simple table.
        $tokens->unshift( $token );
        $tableSpec = $this->readSimpleTableSpecification( $tokens );

        // Refactor specification to work with it more easily.
        $cellStarts = array();
        $position = 1;
        foreach ( $tableSpec as $cell )
        {
            if ( $cell[0] === ezcDocumentRstToken::SPECIAL_CHARS )
            {
                $cellStarts[] = $position;
            }
            $position += $cell[1];
        }

        // Read all titles, which may be multiple rows, each sparated by '-'.
        $titles = array();
        do
        {
            $titles = array_merge(
                $titles,
                $this->readSimpleCells( $cellStarts, $tokens )
            );
        } while ( isset( $tokens[0] ) &&
                  ( $tokens[0]->type == ezcDocumentRstToken::SPECIAL_CHARS ) &&
                  ( $tokens[0]->content[0] === '-' ) &&
                  ( $tokens[0]->position === 1 ) &&
                  ( isset( $tokens[1] ) ) &&
                  ( ( $tokens[1]->type == ezcDocumentRstToken::WHITESPACE ) ||
                    ( $tokens[1]->type == ezcDocumentRstToken::NEWLINE ) ) &&
                  // We ignore the actual header undeline table cell
                  // redefinition, as we detect this magically while reading
                  // the cells already.
                  $this->readSimpleTableSpecification( $tokens ) );

        // After the titles we get another specification line, which should
        // match the top specification
        if ( $tableSpec !== $this->readSimpleTableSpecification( $tokens ) )
        {
            $this->triggerError(
                E_WARNING,
                'Table specification mismatch in simple table.',
                null, $token->line, $token->position
            );
        }

        if ( !isset( $tokens[0] ) ||
             ( $tokens[0]->type === ezcDocumentRstToken::NEWLINE ) )
        {
            // The simple table only contains a body
            $contents = $titles;
            $titles = array();
        }
        else
        {
            $contents = array();
            do
            {
                $contents = array_merge(
                    $contents,
                    $this->readSimpleCells( $cellStarts, $tokens )
                );
            } while ( isset( $tokens[0] ) &&
                      ( $tokens[0]->type == ezcDocumentRstToken::SPECIAL_CHARS ) &&
                      ( $tokens[0]->content[0] === '-' ) &&
                      ( $tokens[0]->position === 1 ) &&
                      ( isset( $tokens[1] ) ) &&
                      ( $tokens[1]->type == ezcDocumentRstToken::WHITESPACE ) &&
                      // We ignore the actual header undeline table cell
                      // redefinition, as we detect this magically while reading
                      // the cells already.
                      $this->readSimpleTableSpecification( $tokens ) );

            // After the titles we get another specification line, which should
            // match the top specification
            if ( $tableSpec !== $this->readSimpleTableSpecification( $tokens ) )
            {
                $this->triggerError(
                    E_WARNING,
                    'Table specification mismatch in simple table.',
                    null, $token->line, $token->position
                );
            }
        }

        // Reenter parser for table titels and contents, and create table AST
        // from it.
        $table = new ezcDocumentRstTableNode( $token );
        if ( count( $titles ) )
        {
            $table->nodes[] = $head = new ezcDocumentRstTableHeadNode( $token );
            $lastCell = null;
            $lastCNr  = null;
            foreach ( $titles as $rNr => $row )
            {
                $head->nodes[$rNr] = $tableRow = new ezcDocumentRstTableRowNode( $token );

                foreach ( $row as $cNr => $cell )
                {
                    /* DEBUG
                    echo "\n   -> Reenter parser for tokens $rNr, $cNr\n";
                    // /DEBUG */
                    $section = $this->reenterParser( $cell );
                    $tableRow->nodes[$cNr] = $tableCell = new ezcDocumentRstTableCellNode( reset( $cell ) );
                    $tableCell->nodes = $section->nodes;

                    // Set colspan, if required
                    if ( ( $lastCNr !== null ) &&
                         ( $lastCNr < ( $cNr - 1 ) ) )
                    {
                        $lastCell->colspan = $cNr - $lastCNr;
                    }

                    $lastCNr = $cNr;
                    $lastCell = $tableCell;
                }

                $lastCNr = null;
            }
        }

        $table->nodes[] = $body = new ezcDocumentRstTableBodyNode( $token );
        $lastCell = null;
        $lastCNr  = null;
        foreach ( $contents as $rNr => $row )
        {
            $body->nodes[$rNr] = $tableRow = new ezcDocumentRstTableRowNode( $token );

            foreach ( $row as $cNr => $cell )
            {
                /* DEBUG
                echo "\n   -> Reenter parser for tokens $rNr, $cNr\n";
                // /DEBUG */
                $section = $this->reenterParser( $cell );
                $tableRow->nodes[$cNr] = $tableCell = new ezcDocumentRstTableCellNode( reset( $cell ) );
                $tableCell->nodes = $section->nodes;

                // Set colspan, if required
                if ( ( $lastCNr !== null ) &&
                     ( $lastCNr < ( $cNr - 1 ) ) )
                {
                    $lastCell->colspan = $cNr - $lastCNr;
                }

                $lastCNr = $cNr;
                $lastCell = $tableCell;
            }

            $lastCNr = null;
        }

        return $table;
    }

    /**
     * Read grid table specifications
     *
     * Read the column specification headers of a grid table and return the
     * sizes of the specified columns in an array.
     *
     * @param ezcDocumentRstStack $tokens
     * @return array
     */
    protected function readGridTableSpecification( &$tokens )
    {
        // Detect the cell sizes inside of the grid table.
        $tableSpec = array();
        /* DEBUG
        echo "  -> Table specification: ";
        // /DEBUG */
        $i = -1;
        do {
            ++$i;
            while ( $tokens[$i]->type !== ezcDocumentRstToken::NEWLINE )
            {
                if ( ( $tokens[$i]->content === '+' ) &&
                     ( ( isset( $tokens[$i - 1] ) ) &&
                       ( ( $tokens[$i - 1]->type === ezcDocumentRstToken::NEWLINE ) ||
                         ( ( $tokens[$i - 1]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                           ( ( $tokens[$i - 1]->content[0] === '=' ) ||
                             ( $tokens[$i - 1]->content[0] === '-' ) ) ) ) ) &&
                     ( ( isset( $tokens[$i + 1] ) ) &&
                       ( ( $tokens[$i + 1]->type === ezcDocumentRstToken::NEWLINE ) ||
                         ( ( $tokens[$i + 1]->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                           ( ( $tokens[$i + 1]->content[0] === '=' ) ||
                             ( $tokens[$i + 1]->content[0] === '-' ) ) ) ) ) )
                {
                    $tableSpec[] = $tokens[$i]->position;
                }

                ++$i;
            }
        } while ( ( $tokens[$i]->type !== ezcDocumentRstToken::NEWLINE ) ||
                  ( $tokens[$i + 1]->type !== ezcDocumentRstToken::NEWLINE ) );
        /* DEBUG
        echo "\n";
        // /DEBUG */

        $tableSpec = array_unique( $tableSpec );
        sort( $tableSpec );

        return $tableSpec;
    }

    /**
     * Shift grid table
     *
     * In "Grid tables" the values are embedded in a complete grid visually
     * describing a a table using characters.
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#grid-tables
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftGridTable( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        if ( ( $token->position > 1 ) ||
             ( $token->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $token->content !== '+' ) ||
             ( !isset( $tokens[0] ) ) ||
             ( $tokens[0]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $tokens[0]->content[0] !== '-' ) ||
             ( !isset( $tokens[1] ) ) ||
             ( $tokens[1]->type !== ezcDocumentRstToken::SPECIAL_CHARS ) ||
             ( $tokens[1]->content !== '+' ) )
        {
            // Missing multiple special character groups only containing '=',
            // separated by whitespaces, which introduce a simple table.
            return false;
        }

        /* DEBUG
        echo "  -> Found grid table.\n";
        // /DEBUG */
        // Detect the cell sizes inside of the grid table.
        $rowOffset = $token->line;
        $tokens->unshift( $token );
        $tableSpec = $this->readGridTableSpecification( $tokens );

        // Read all table tokens and extract the complete cell specification of
        // the table.
        $tableTokens = array();
        $titleRow    = 0;
        $cells       = array();
        $row         = 0;
        while ( ( $tableTokens[] = $token = $tokens->shift() ) &&
                // Read until we find two newlines, which indicate the end of
                // the table
                ( ( $token->type !== ezcDocumentRstToken::NEWLINE ) ||
                  ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ) )
        {
            if ( ( $position = array_search( $token->position, $tableSpec, true ) ) !== false )
            {
                /* DEBUG
                echo "    -> Token at cell position: ";
                // /DEBUG */
                // Token may be relevant for the table structure, as it resides
                // at the entry points of the table specification.
                switch ( true )
                {
                    case ( ( $token->content === '+' ) &&
                           ( isset( $tokens[0] ) ) &&
                           ( $tokens[0]->content[0] === '=' ) ):
                        $titleRow = $row;

                    case ( ( $token->content === '+' ) &&
                           ( isset( $tokens[0] ) ) &&
                           ( $tokens[0]->content[0] === '-' ) ):
                        /* DEBUG
                        echo "Row breaker: $position\n";
                        // /DEBUG */
                        $cells[$row][$position] = ( $tokens[0]->content[0] === '-' ? 2 : 3 );
                        break;

                    case ( ( ( $token->content === '|' ) &&
                             ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ) ||
                           ( ( $token->content === '+' ) &&
                             ( $tokens[0]->type !== ezcDocumentRstToken::NEWLINE ) ) ):
                        /* DEBUG
                        echo "Cell: $position\n";
                        // /DEBUG */
                        $cells[$row][$position] = 1;
                        break;

                    default:
                        /* DEBUG
                        echo "irrelevant\n";
                        // /DEBUG */
                }
            }
            elseif ( $token->type === ezcDocumentRstToken::NEWLINE )
            {
                /* DEBUG
                echo "   -> Next row.\n";
                // /DEBUG */
                ++$row;
            }
        }

        // Dump cell structure
        /* DEBUG
        foreach ( $cells as $rNr => $row )
        {
            $lcNr = 0;
            foreach ( $row as $cNr => $cell )
            {
                for ( $i = $lcNr; $i < ( $cNr - 1 ); ++$i ) echo "    ";
                echo ( $cell === 1 ? '|>  ' : ( $cell === 2 ? '----' : '====' ) );
                $lcNr = $cNr;
            }
            echo "\n";
        }
        // /DEBUG */

        // Clean up cell structure: Remove cell seperators, which actually
        // aren't cell seperators because they are not followed or preceeded by
        // cell seperators.
        /* DEBUG
        echo "  -> Clean up cell structure\n";
        // /DEBUG */
        $rowCount = count( $cells );
        foreach ( $cells as $rNr => $row )
        {
            foreach ( $row as $cNr => $cell )
            {
                if ( $cell !== 1 )
                {
                    // Skip everything but cell seperators
                    continue;
                }

                if ( ( $rNr > 0 ) &&
                     ( !isset( $cells[$rNr - 1][$cNr] ) ) )
                {
                    /* DEBUG
                    echo "   -> Remove superflous cell seperator (NP) in $rNr * $cNr\n";
                    // /DEBUG */
                    unset( $cells[$rNr][$cNr] );
                }
                elseif ( ( $rNr < ( $rowCount - 1 ) ) &&
                         ( !isset( $cells[$rNr + 1][$cNr] ) ) )
                {
                    /* DEBUG
                    echo "   -> Remove superflous cell seperator (NF) in $rNr * $cNr\n";
                    // /DEBUG */
                    unset( $cells[$rNr][$cNr] );
                }
            }
        }

        // Dump cell structure
        /* DEBUG
        foreach ( $cells as $rNr => $row )
        {
            $lcNr = 0;
            foreach ( $row as $cNr => $cell )
            {
                for ( $i = $lcNr; $i < ( $cNr - 1 ); ++$i ) echo "    ";
                echo ( $cell === 1 ? '|>  ' : ( $cell === 2 ? '----' : '====' ) );
                $lcNr = $cNr;
            }
            echo "\n";
        }
        // /DEBUG */

        $columnNumber = array();
        $cellMapping  = array();
        $rowCount     = count( $cells );
        $cellCount    = count( $tableSpec ) - 1;

        // Initilize column number array
        for ( $c = 0; $c < $cellCount; ++$c )
        {
            $rowNumber[$c] = 0;
        }

        // Create cell mapping array
        for ( $r = 0; $r < $rowCount; ++$r )
        {
            for ( $c = 0; $c < $cellCount; ++$c )
            {
                if ( !isset( $cells[$r][$c] ) )
                {
                    // No explicit cell definition given. Map to last cell in
                    // current row.
                    $row = $rowNumber[$c];

                    // It may happen for cell seperators, that the last cell is
                    // not available. It is save to skip this case.
                    if ( !isset( $cellMapping[$r][$c - 1] ) )
                    {
                        continue;
                    }

                    $lastCell = $cellMapping[$r][$c - 1];
                    if ( ( $lastCell[0] !== $row ) ||
                         ( $lastCell[1] !== ( $c - 1 ) ) )
                    {
                        // Last cell has already been mapped, use the map
                        // destination from this cell. We do not need to do
                        // this recusively, because we iterate in single steps
                        // over the table and fix each redirection immediately,
                        // so all prior cells already point to their final
                        // location.
                        $cellMapping[$r][$c] = $lastCell;
                    }
                    else
                    {
                        // Otherwise map to last cell
                        $cellMapping[$r][$c] = array( $row, $c - 1 );
                    }
                }
                elseif ( $cells[$r][$c] === 1 )
                {
                    // New cell, just add to mapping table.
                    $cellMapping[$r][$c] = array( $rowNumber[$c], $c );
                }
                elseif ( $cells[$r][$c] > 1 )
                {
                    // We found a row breaker, so increase the future row
                    // number for the current column.
                    //
                    // The increased column number is the maximum of the
                    // current column + 1 and all other columns, because we
                    // want to keep up to a same row number in one row.
                    $rowNumber[$c] = max(
                        $rowNumber[$c] + 1,
                        max( $rowNumber ),
                        max( array_slice( $rowNumber, $c ) ) + 1
                    );
                }
            }
        }

        /* DEBUG
        foreach ( $cellMapping as $rNr => $row )
        {
            echo $rNr, ": ";
            foreach ( $row as $cNr => $cell )
            {
                echo "$cNr(", $cell[0], ", ", $cell[1], ")  ";
            }
            echo "\n";
        }
        // /DEBUG */

        // Iterate over cell mapping array to calculate cell spans
        $cellSpans = array();
        $rNr = 1;
        foreach ( $cellMapping as $nr => $row )
        {
            // Determine maximum row number in current row
            $maxNr = 0;
            foreach ( $row as $cell )
            {
                $maxNr = max( $maxNr, $cell[0] );
            }

            if ( $rNr > $maxNr )
            {
                continue;
            }

            // Increase row and colspan depending on the cell pointer.
            foreach ( $row as $cNr => $cell )
            {
                if ( ( $rNr === $cell[0] ) &&
                     ( $cNr === $cell[1] ) )
                {
                    // It is the cell itself
                    $cellSpans[$cell[0]][$cell[1]] = array( 1, 1 );
                }
                elseif ( $cNr === $cell[1] )
                {
                    // Another cell pointer in the same column
                    $cellSpans[$cell[0]][$cell[1]][0]++;
                }
                elseif ( $rNr === $cell[0] )
                {
                    // Another cell pointer in the same row
                    $cellSpans[$cell[0]][$cell[1]][1]++;
                }
            }

            $rNr = $maxNr + 1;
        }

        // Now we can reiterate over the cell tokens array and assign all
        // tokens to their correct cells.
        $cell     = 0;
        $row      = 0;
        $contents = array();
        $titles   = array();
        $current  = &$titles;
        foreach ( $tableTokens as $token )
        {
            // Newline tokens are only used to skip into the next row, but
            // should also be added to each cell, to maintain the wrapping
            // iside of cells.
            if ( $token->type === ezcDocumentRstToken::NEWLINE )
            {
                if ( $row === $titleRow )
                {
                    // Switch current cell storage to table contents, once we
                    // got past the title row seperator.
                    $current = &$contents;
                }

                // Sppend the newline token to all current cells
                foreach ( $tableSpec as $col => $pos )
                {
                    if ( isset( $cellMapping[$row] ) && isset( $cellMapping[$row][$col] ) )
                    {
                        list( $r, $c ) = $cellMapping[$row][$col];
                        $current[$r][$c][] = $token;
                    }
                }

                ++$row;
                $cell = 0;
                continue;
            }

            // Check if this is a spec token, we want to ignore.
            if ( ( ( $position = array_search( $token->position, $tableSpec, true ) ) !== false ) &&
                 ( ( isset( $cells[$row][$position] ) ) ||
                   ( ( $position + 1 ) >= count( $tableSpec ) ) ) )
            {
                // Skip spec token.
                continue;
            }

            // Check if entered the next column by checking the current token
            // position as the column offsets in the table spcification.
            if ( $token->position >= $tableSpec[$cell + 1] )
            {
                ++$cell;
            }

            // Get the actual destination cell from the table mapping array. If
            // there is no entry in the cell mapping array, the token is a
            // column breaker, and can safely be ignored.
            if ( isset( $cellMapping[$row] ) && isset( $cellMapping[$row][$cell] ) )
            {
                list( $r, $c ) = $cellMapping[$row][$cell];
                $current[$r][$c][] = $token;
            }
        }

        /* DEBUG
        echo "  -> Table contents:\n";
        foreach ( $contents as $rNr => $row )
        {
            echo $rNr, ": ";
            foreach ( $row as $cNr => $cell )
            {
                printf( '% 4d ', count( $cell ) );
            }
            echo "\n";
        }
        // /DEBUG */

        // Reenter parser for table titels and contents, and create table AST
        // from it.
        $table = new ezcDocumentRstTableNode( $token );
        if ( count( $titles ) )
        {
            $table->nodes[] = $head = new ezcDocumentRstTableHeadNode( $token );
            foreach ( $titles as $rNr => $row )
            {
                $head->nodes[$rNr] = $tableRow = new ezcDocumentRstTableRowNode( $token );

                foreach ( $row as $cNr => $cell )
                {
                    /* DEBUG
                    echo "\n   -> Reenter parser for tokens $rNr, $cNr\n";
                    // /DEBUG */
                    $section = $this->reenterParser( $cell );
                    $tableRow->nodes[$cNr] = $tableCell = new ezcDocumentRstTableCellNode( reset( $cell ) );
                    $tableCell->nodes = $section->nodes;
                    $tableCell->rowspan = $cellSpans[$rNr][$cNr][0];
                    $tableCell->colspan = $cellSpans[$rNr][$cNr][1];
                }
            }
        }

        $table->nodes[] = $body = new ezcDocumentRstTableBodyNode( $token );
        foreach ( $contents as $rNr => $row )
        {
            $body->nodes[$rNr] = $tableRow = new ezcDocumentRstTableRowNode( $token );

            foreach ( $row as $cNr => $cell )
            {
                /* DEBUG
                echo "\n   -> Reenter parser for tokens $rNr, $cNr\n";
                // /DEBUG */
                $section = $this->reenterParser( $cell );
                $tableRow->nodes[$cNr] = $tableCell = new ezcDocumentRstTableCellNode( reset( $cell ) );
                $tableCell->nodes = $section->nodes;
                $tableCell->rowspan = $cellSpans[$rNr][$cNr][0];
                $tableCell->colspan = $cellSpans[$rNr][$cNr][1];
            }
        }

        return $table;
    }

    /**
     * Shift definition lists
     *
     * Shift definition lists, which are introduced by an indentation change
     * without speration by a paragraph. Because of this the method is called
     * form the updateIndentation method, which handles such indentation
     * changes.
     *
     * The text for the definition and its classifiers is already on the
     * document stack because of this.
     *
     * Definition lists are specified at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#definition-lists
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstStack $tokens
     * @return ezcDocumentRstMarkupEmphasisNode
     */
    protected function shiftDefinitionList( ezcDocumentRstToken $token, ezcDocumentRstStack $tokens )
    {
        // Fetch definition list back from document stack, where the text nodes
        // are stacked in reverse order.
        $name = array();
        /* DEBUG
        echo "  -> Fetch name from document stack: ";
        // /DEBUG */
        do {
            $node = $this->documentStack->shift();
            $name[] = $node->token;
            /* DEBUG
            echo '.';
            // /DEBUG */
        } while ( isset( $this->documentStack[0] ) &&
                  in_array( $this->documentStack[0]->type, $this->textNodes, true ) );
        /* DEBUG
        echo "\n";
        // /DEBUG */

        $node = new ezcDocumentRstDefinitionListNode( $token, array_reverse( $name ) );
        // With the name we find the associated contents, which
        // may span multiple lines, so that this is done by a
        // seperate method.
        $tokens->unshift( $token );
        /* DEBUG
        echo "  -> Read definition list contents\n";
        // /DEBUG */
        $content = $this->readMutlipleIndentedLines( $tokens, true );
        array_shift( $content );
        $section = $this->reenterParser( $content );
        $node->nodes = $section instanceof ezcDocumentRstDocumentNode ? $section->nodes : $section;

        // There is nothing more to read. We can exit immediately.
        return $node;
    }

    /**
     * Reduce all elements to one document node.
     *
     * @param ezcDocumentRstTitleNode $node
     * @return void
     */
    protected function reduceTitle( ezcDocumentRstTitleNode $node )
    {
        if ( !isset( $this->documentStack[0] ) ||
             !in_array( $this->documentStack[0]->type, $this->textNodes, true ) )
        {
            // This is a title top line, just skip for now.
            return $node;
        }

        // Pop all text lines from stack and aggregate them into the title
        $nodes           = array();
        $titleTextLength = 0;
        while ( ( isset( $this->documentStack[0] ) ) &&
                in_array( $this->documentStack[0]->type, $this->textNodes, true ) )
        {
            $nodes[] = $textNode = $this->documentStack->shift();
            /* DEBUG
            echo "  -> Add ", ezcDocumentRstNode::getTokenName( $textNode->type ), " to title.\n";
            // /DEBUG */

            if ( ( $titleTextLength > 0 ) ||
                 ( $textNode->token->type !== ezcDocumentRstToken::WHITESPACE ) )
            {
                $titleTextLength += iconv_strlen( $textNode->token->content, 'UTF-8' );
            }
        }

        // Trim whitespaces in text nodes.
        if ( isset( $nodes[0] ) &&
             ( $nodes[0]->type === ezcDocumentRstNode::TEXT_LINE ) )
        {
            $nodes[0]->token->content = rtrim( $nodes[0]->token->content );
        }

        $nodes = array_reverse( $nodes );
        if ( isset( $nodes[0] ) &&
             ( $nodes[0]->type === ezcDocumentRstNode::TEXT_LINE ) )
        {
            $nodes[0]->token->content = rtrim( $nodes[0]->token->content );
        }

        $node->nodes = $nodes;

        // There is one additional whitespace appended because of the newline -
        // remove it:
        --$titleTextLength;

        // Check if the lengths of the top line and the text matches.
        if ( ( $titleLength = iconv_strlen( $node->token->content, 'UTF-8' ) ) < $titleTextLength )
        {
            $this->triggerError(
                E_NOTICE,
                "Title underline length ({$titleLength}) is shorter then text length ({$titleTextLength}).",
                null, $node->token->line, $node->token->position
            );
        }

        // Check if the title has a top line
        $titleType = $node->token->content[0];
        if ( isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0]->type === ezcDocumentRstNode::TITLE ) )
        {
            $doubleTitle = $this->documentStack->shift();
            $titleType = $doubleTitle->token->content[0] . $titleType;

            // Ensure title over and underline lengths matches, for docutils
            // this is a severe error.
            if ( iconv_strlen( $node->token->content, 'UTF-8' ) !== iconv_strlen( $doubleTitle->token->content, 'UTF-8' ) )
            {
                $this->triggerError(
                    E_WARNING,
                    "Title overline and underline mismatch.",
                    null, $node->token->line, $node->token->position
                );
            }
        }

        // Get section nesting depth for title
        if ( isset( $this->titleLevels[$titleType] ) )
        {
            $depth = $this->titleLevels[$titleType];
        }
        else
        {
            $this->titleLevels[$titleType] = $depth = count( $this->titleLevels ) + 1;
        }

        // Prepend section element to document stack
        return new ezcDocumentRstSectionNode(
            $node->token, $node, $depth
        );
    }

    /**
     * Reduce prior sections, if a new section has been found.
     *
     * If a new section has been found all sections with a higher depth level
     * can be closed, and all items fitting into sections may be aggregated by
     * the respective sections as well.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceSection( ezcDocumentRstNode $node )
    {
        // Collected node for prior section
        $collected = array();
        $lastSectionDepth = -1;

        // Include all paragraphs, tables, lists and sections with a higher
        // nesting depth
        while ( $child = $this->documentStack->shift() )
        {
            /* DEBUG
            echo "  -> Try node: " . ezcDocumentRstNode::getTokenName( $child->type ) . ".\n";
            // /DEBUG */
            if ( !in_array( $child->type, $this->blockNodes, true ) )
            {
                $this->triggerError(
                    E_PARSE,
                    "Unexpected node: " . ezcDocumentRstNode::getTokenName( $child->type ) . ".",
                    null, $child->token->line, $child->token->position
                );
            }

            if ( $child->type === ezcDocumentRstNode::SECTION )
            {
                if ( ( $child->depth <= $node->depth ) &&
                     ( $node->depth !== 0 ) )
                {
                    // If the found section has a same or higher level, just
                    // put everything back on the stack
                    $child->nodes = array_merge(
                        $child->nodes,
                        $collected
                    );
                    $this->documentStack->unshift( $child );

                    /* DEBUG
                    echo "   -> Leave on stack.\n";
                    $this->dumpStack();
                    // /DEBUG */
                    return $node;
                }

                // Reduce document, if reached
                if ( ( $child->depth == 0 ) &&
                     ( $node->depth == 0 ) )
                {
                    /* DEBUG
                    echo "   -> Aggregate in root document node.\n";
                    // /DEBUG */
                    $child->nodes = array_merge(
                        $child->nodes,
                        $collected
                    );
                    return $child;
                }

                // Check for title depth incosistency
                if ( ( $lastSectionDepth - $child->depth ) > 1 )
                {
                    $this->triggerError(
                        E_PARSE,
                        "Title depth inconsitency.",
                        null, $child->token->line, $child->token->position
                    );
                }

                if ( ( $lastSectionDepth === -1 ) ||
                     ( $lastSectionDepth > $child->depth ) )
                {
                    // If the section level is higher then in our new node and
                    // lower the the last node, reduce sections.
                    /* DEBUG
                    echo "   -> Reduce section {$child->depth}.\n";
                    // /DEBUG */
                    $child->nodes = array_merge(
                        $child->nodes,
                        $collected
                    );
                    $collected = array();
                }

                // Sections on an equal level are just appended, for all
                // sections we remember the last depth.
                $lastSectionDepth = $child->depth;
            }

            /* DEBUG
            echo "  -> Found another child...\n";
            // /DEBUG */
            array_unshift( $collected, $child );
        }

        // No reduction found, put things back on stack.
        /* DEBUG
        echo "   -> Put everything back on stack.\n";
        // /DEBUG */
        $this->documentStack->prepend( array_reverse( $collected ) );
        return $node;
    }

    /**
     * Reduce blockquote annotation content
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceBlockquoteAnnotationParagraph( ezcDocumentRstNode $node )
    {
        if ( isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0]->type === ezcDocumentRstNode::ANNOTATION ) )
        {
            // The last paragraph was preceded by an annotation marker
            $annotation = $this->documentStack->shift();
            $annotation->nodes = $node;
            return $annotation;
        }

        return $node;
    }

    /**
     * Reduce blockquote annotation
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceBlockquoteAnnotation( ezcDocumentRstNode $node )
    {
        // Do not reduce before it is filled with content
        if ( count( $node->nodes ) < 1 )
        {
            return $node;
        }

        // It has already ensured by the shift, that the marker is preceeded by
        // a blockquote.
        $this->documentStack[0]->annotation = $node;
        $this->documentStack[0]->closed = true;
        return null;
    }

    /**
     * Reduce paragraph to blockquote
     *
     * Indented paragraphs are blockquotes, which should be wrapped in such a
     * node.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceBlockquote( ezcDocumentRstNode $node )
    {
        if ( $node->indentation <= 0 )
        {
            // Apply rule only for indented paragraphs.
            return $node;
        }

        // Check last node, if it is already a blockquote, append paragraph
        // there.
        if ( isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0]->type === ezcDocumentRstNode::BLOCKQUOTE ) &&
             ( $this->documentStack[0]->closed === false ) &&
             ( $this->documentStack[0]->indentation <= $node->indentation ) )
        {
            // Just append paragraph and exit
            $quote = $this->documentStack->shift();
            $quote->nodes[] = $node;
            return $quote;
        }

        // Create a new blockquote
        $blockquote = new ezcDocumentRstBlockquoteNode( $node->nodes[0]->token );
        array_unshift( $blockquote->nodes, $node );

        // Set blockquote indentation to the indentation of the last item on
        // the document stack. This way it can be handled like other block
        // level elements and already has the special markup.
        $blockquote->indentation = isset( $this->documentStack[0] ) && isset( $this->documentStack[0]->indentation ) ? $this->documentStack[0]->indentation : 0;

        return $blockquote;
    }

    /**
     * Reduce paragraph to bullet lsit
     *
     * Indented paragraphs are bllet lists, if prefixed by a bullet list
     * indicator.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceListItem( ezcDocumentRstNode $node )
    {
        $childs = array();
        $lastIndentationLevel = $node->indentation;

        // If this is the very first paragraph, we have nothing we can reduce
        // to, so just skip this rule.
        if ( !count( $this->documentStack ) )
        {
            /* DEBUG
            echo "  => Nothing to reduce to.\n";
            // /DEBUG */
            return $node;
        }

        // Include all paragraphs, lists and blockquotes
        while ( $child = $this->documentStack->shift() )
        {
            if ( ( !$child instanceof ezcDocumentRstBlockNode ) ||
                 ( $child->indentation < $node->indentation ) )
            {
                // We did not find a bullet list to reduce to, so it is time to
                // put the stuff back to the stack and leave.
                /* DEBUG
                echo "   -> No reduction target found, reached ", ezcDocumentRstNode::getTokenName( $child->type ), ".\n";
                // /DEBUG */
                $this->documentStack->prepend( array_merge(
                    $childs,
                    array( $child )
                ) );
                return $node;
            }

            if ( ( ( $child->type === ezcDocumentRstNode::ENUMERATED_LIST ) ||
                   ( $child->type === ezcDocumentRstNode::BULLET_LIST ) ) &&
                 ( $child->indentation === $node->indentation ) )
            {
                // We found a bullet list for the current paragraph.
                /* DEBUG
                echo "   => Found matching list.\n";
                // /DEBUG */
                $child->nodes = array_merge(
                    $child->nodes,
                    array_reverse( $childs ),
                    array( $node )
                );
                $this->documentStack->prepend( array( $child ) );
                return null;
            }

            if ( ( ( $child->type === ezcDocumentRstNode::ENUMERATED_LIST ) ||
                   ( $child->type === ezcDocumentRstNode::BULLET_LIST ) ) &&
                 ( $child->indentation < $lastIndentationLevel ) )
            {
                // The indentation level reduced during the processing of
                // childs. We can reduce the found childs to the new child with
                // lowest indentation.
                /* DEBUG
                echo "   -> Reduce subgroup (" . count( $childs ) . " items).\n";
                // /DEBUG */
                $child->nodes = array_merge(
                    $child->nodes,
                    array_reverse( $childs )
                );
                $childs = array();
            }

            // Else just append item to curernt child list, and update current
            // indentation.
            /* DEBUG
            echo "   -> Appending " . ezcDocumentRstNode::getTokenName( $child->type ) . ".\n";
            // /DEBUG */
            $childs[] = $child;
            $lastIndentationLevel = $child->indentation;
        }

        // Clean up and return node
        $this->documentStack->prepend( array_merge(
            array( $node ),
            $childs
        ) );
        /* DEBUG
        echo "   => Done (" . count( $this->documentStack ) . " elements on stack).\n";
        // /DEBUG */
        return null;
    }

    /**
     * Reduce item to bullet list
     *
     * Called for all items, which may be part of bullet lists. Depending on
     * the indentation level we reduce some amount of items to a bullet list.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceList( ezcDocumentRstNode $node )
    {
        $childs = array();
        $lastIndentationLevel = 0;

        $nodeIndentation = $node instanceof ezcDocumentRstBlockNode ? $node->indentation : 0;

        /* DEBUG
        echo "   - Indentation {$nodeIndentation}.\n";
        // /DEBUG */

        // Include all paragraphs, lists and blockquotes
        while ( $child = $this->documentStack->shift() )
        {
            if ( ( !$child instanceof ezcDocumentRstBlockNode ) ||
                 ( $child->indentation < $nodeIndentation ) )
            {
                // We did not find a list to reduce to, so it is time to put
                // the stuff back to the stack and leave.
                /* DEBUG
                echo "   -> No reduction target found, reached ", ezcDocumentRstNode::getTokenName( $child->type ), ".\n";
                // /DEBUG */
                $this->documentStack->unshift( $child );
                break;
            }

            if ( ( $child->indentation === $nodeIndentation ) &&
                 ( $child->type === $node->type ) &&
                 ( ( $child->type === ezcDocumentRstNode::ENUMERATED_LIST ) ||
                   ( $child->type === ezcDocumentRstNode::BULLET_LIST ) ) )
            {
                // We found a list on the same level, so this is a new list
                // item.
                /* DEBUG
                echo "   => Found same level list item.\n";
                // /DEBUG */
                $child->nodes = array_merge(
                    $child->nodes,
                    $childs
                );

                $this->documentStack->prepend( array( $child ) );
                return $node;
            }

            if ( ( ( $child->type === ezcDocumentRstNode::ENUMERATED_LIST ) ||
                   ( $child->type === ezcDocumentRstNode::BULLET_LIST ) ) &&
                 ( $child->indentation < $lastIndentationLevel ) )
            {
                // The indentation level reduced during the processing of
                // childs. We can reduce the found childs to the new child with
                // lowest indentation.
                /* DEBUG
                echo "   -> Reduce subgroup (" . count( $childs ) . " items).\n";
                // /DEBUG */
                $child->nodes = array_merge(
                    $child->nodes,
                    array_reverse( $childs )
                );
                $childs = array();
            }

            // Else just append item to curernt child list, and update current
            // indentation.
            /* DEBUG
            echo "   -> Appending " . ezcDocumentRstNode::getTokenName( $child->type ) . ".\n";
            // /DEBUG */
            $childs[] = $child;
            $lastIndentationLevel = $child->indentation;
        }

        // Clean up and return node
        /* DEBUG
        echo "   => Done.\n";
        // /DEBUG */
        $this->documentStack->prepend( $childs );
        return $node;
    }

    /**
     * Reduce paragraph
     *
     * Aggregates all nodes which are allowed as subnodes into a paragraph.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceParagraph( ezcDocumentRstNode $node )
    {
        $found = 0;

        // Include all paragraphs, tables, lists and sections with a higher
        // nesting depth
        $nodes = array();
        while ( isset( $this->documentStack[0] ) &&
            in_array( $this->documentStack[0]->type, $this->textNodes, true ) )
        {
            // Convert single markup nodes back to text
            $text = $this->documentStack->shift();
            if ( in_array( $text->type, array(
                    ezcDocumentRstNode::MARKUP_EMPHASIS,
                    ezcDocumentRstNode::MARKUP_STRONG,
                    ezcDocumentRstNode::MARKUP_INTERPRETED,
                    ezcDocumentRstNode::MARKUP_LITERAL,
                    ezcDocumentRstNode::MARKUP_SUBSTITUTION,
                 ) ) &&
                 ( count( $text->nodes ) < 1 ) )
            {
                $text = new ezcDocumentRstTextLineNode( $text->token );
            }

            /* DEBUG
            echo "   -> Append text to paragraph\n";
            // /DEBUG */
            array_unshift( $nodes, $text );
            ++$found;
        }

        if ( $found > 0 )
        {
            // Reduce text nodes in single AST nodes
            $textNode = null;
            foreach ( $nodes as $nr => $child )
            {
                if ( $child->type === ezcDocumentRstNode::TEXT_LINE )
                {
                    if ( $textNode === null )
                    {
                        $textNode = $child;
                    }
                    else
                    {
                        $textNode->token->content .= $child->token->content;
                        unset( $nodes[$nr] );
                    }
                }
                else
                {
                    if ( $textNode !== null )
                    {
                        $textNode = null;
                    }
                }
            }

            // Find inline markup in all paragraph text nodes
            $linkedNodes = array();
            foreach ( $nodes as $nr => $child )
            {
                if ( $child->type === ezcDocumentRstNode::TEXT_LINE )
                {
                    // Find links in text nodes
                    while ( preg_match( self::REGEXP_INLINE_LINK, $child->token->content, $match ) )
                    {
                        $urlPosition = strpos( $child->token->content, $match['match'] );

                        // Create new token from text before URL.
                        $textToken = clone $child->token;
                        $textToken->content = substr( $child->token->content, 0, $urlPosition );
                        $linkedNodes[] = new ezcDocumentRstTextLineNode( $textToken );

                        // Create linked node from found URL
                        $linkToken = clone $child->token;
                        $linkToken->position += $urlPosition;
                        $linkToken->content   = $match['match'];
                        $newChild = new ezcDocumentRstExternalReferenceNode( $linkToken );
                        $newChild->target = ( isset( $match['mail'] ) ? 'mailto:' . $match['mail'] : $match['url'] );
                        $newChild->nodes = array(
                            new ezcDocumentRstTextLineNode( $linkToken ),
                        );
                        $linkedNodes[] = $newChild;

                        // Check the child for other URLs
                        $child->token->position += $offset = $urlPosition + strlen( $match['match'] );
                        $child->token->content   = substr( $child->token->content, $offset );
                        $child->position         = $child->token->position;
                    }
                }

                // Add child, which had no more matches, or isn't plain inline text.
                $linkedNodes[] = $child;
            }

            $node->indentation = $this->indentation;
            $node->nodes = $linkedNodes;
            /* DEBUG
            echo "   => Create paragraph with indentation {$node->indentation} (postIndent: {$this->postIndentation})\n";
            // /DEBUG */
            $this->indentation = ( $this->postIndentation !== null ? $this->postIndentation : 0 );
            return $node;
        }
    }

    /**
     * Reduce markup
     *
     * Tries to find the opening tag for a markup definition.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceMarkup( ezcDocumentRstNode $node )
    {
        if ( $node->openTag === true )
        {
            // Opening tags are just added to the document stack and we exit
            // the reuction method.
            return $node;
        }

        $childs = array();
        while ( isset( $this->documentStack[0] ) &&
            in_array( $this->documentStack[0]->type, $this->textNodes, true ) )
        {
            $child = $this->documentStack->shift();
            if ( ( $child->type == $node->type ) &&
                 ( $child->openTag === true ) )
            {
                /* DEBUG
                echo "   => Found matching tag.\n";
                // /DEBUG */
                // We found the nearest matching open tag. Append all included
                // stuff as child nodes and add the closing tag to the document
                // stack.
                $node->nodes = $childs;
                return $node;
            }

            /* DEBUG
            echo "     - Collected " . ezcDocumentRstNode::getTokenName( $child->type ) . " ({$child->token->content}).\n";
            // /DEBUG */

            // Append unusable but inline node to potential child list.
            array_unshift( $childs, $child );
        }

        // We did not find an opening node.
        //
        // This is not a parse error, but in this case we just consider the
        // closing node as text and reattach all found childs to the document
        // stack.
        /* DEBUG
        echo "   => Use as Text.\n";
        // /DEBUG */
        $this->documentStack->prepend( array_reverse( $childs ) );

        return new ezcDocumentRstTextLineNode( $node->token );
    }

    /**
     * Reduce interpreted text inline markup
     *
     * Tries to find the opening tag for a markup definition.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceInterpretedText( ezcDocumentRstNode $node )
    {
        if ( $node->openTag === true )
        {
            // Opening tags are just added to the document stack and we exit
            // the reuction method.
            return $node;
        }

        $childs = array();
        while ( isset( $this->documentStack[0] ) &&
                in_array( $this->documentStack[0]->type, $this->textNodes, true ) )
        {
            $child = $this->documentStack->shift();
            if ( ( $child->type == $node->type ) &&
                 ( $child->openTag === true ) )
            {
                /* DEBUG
                echo "   => Found matching tag.\n";
                // /DEBUG */
                // We found the nearest matching open tag. Append all included
                // stuff as child nodes and add the closing tag to the document
                // stack.
                $node->nodes = $childs;

                // Set the role from the opening tag, if defined there
                if ( $child->role )
                {
                    $node->role = $child->role;
                }

                return $node;
            }

            /* DEBUG
            echo "     - Collected " . ezcDocumentRstNode::getTokenName( $child->type ) . " ({$child->token->content}).\n";
            // /DEBUG */

            // Append unusable but inline node to potential child list.
            array_unshift( $childs, $child );
        }

        // We did not find an opening node.
        //
        // This is not a parse error, but in this case we just consider the
        // closing node as text and reattach all found childs to the document
        // stack.
        /* DEBUG
        echo "   => Use as Text.\n";
        // /DEBUG */
        $this->documentStack->prepend( array_reverse( $childs ) );

        return new ezcDocumentRstTextLineNode( $node->token );
    }

    /**
     * Reduce internal target
     *
     * Internal targets are listed before the literal markup block, so it may
     * be found and reduced after we found a markup block.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceInternalTarget( ezcDocumentRstNode $node )
    {
        if ( ( $node->type !== ezcDocumentRstNode::MARKUP_INTERPRETED ) ||
             ( count( $node->nodes ) <= 0 ) )
        {
            // This is a irrelevant markup tags for this rules
            /* DEBUG
            echo "   -> Irrelevant markup.\n";
            // /DEBUG */
            return $node;
        }

        if ( isset( $this->documentStack[0] ) &&
             ( $this->documentStack[0]->type === ezcDocumentRstNode::TEXT_LINE ) &&
             ( $this->documentStack[0]->token->content === '_' ) )
        {
            // We found something, create target node and aggregate
            // corresponding nodes.
            $targetTextNode = $this->documentStack->shift();
            $target = new ezcDocumentRstTargetNode( $targetTextNode->token );
            $target->nodes = array( $node );
            /* DEBUG
            echo "   -> Found new target.\n";
            // /DEBUG */
            return $target;
        }

        // Otherwise just do nothing and pass the old node through
        /* DEBUG
        echo "   -> Skipped: No match.\n";
        // /DEBUG */
        return $node;
    }

    /**
     * Reduce reference
     *
     * Reduce references as defined at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#inline-markup
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceReference( ezcDocumentRstNode $node )
    {
        // Pop closing brace
        $closing = $this->documentStack->shift();

        // Find all childs.
        //
        // May be multiple childs, since the references may consist of multiple
        // characters with special chars ( *, # ) embedded.
        $childs = array();
        while ( isset( $this->documentStack[0] ) &&
                ( $this->documentStack[0]->type === ezcDocumentRstNode::TEXT_LINE ) )
        {
            $child = $this->documentStack->shift();
            if ( ( $child->token->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                 ( $child->token->content === '[' ) )
            {
                /* DEBUG
                echo "   => Found matching tag.\n";
                // /DEBUG */
                // We found the nearest matching open tag. Append all included
                // stuff as child nodes and add the closing tag to the document
                // stack.
                $node->name         = $childs;
                $node->footnoteType = $this->detectFootnotetype( $childs );
                return $node;
            }

            /* DEBUG
            echo "     - Collected " . ezcDocumentRstNode::getTokenName( $child->type ) . ".\n";
            // /DEBUG */

            // Append unusable but inline node to potential child list.
            array_unshift( $childs, $child->token );
        }

        // We did not find an opening node.
        //
        // This is not a parse error, but in this case we just consider the
        // closing node as text and reattach all found childs to the document
        // stack.
        /* DEBUG
        echo "   => Use as Text.\n";
        // /DEBUG */
        $this->documentStack->prepend( array_merge(
            array( $closing ),
            $childs
        ) );

        return new ezcDocumentRstTextLineNode( $node->token );
    }

    /**
     * Reduce link
     *
     * Uses the preceding element as the hyperlink content. This should be
     * either a literal markup section, or just the last word.
     *
     * As we do not get workd content out of the tokenizer (too much overhead),
     * we split out the previous text node up, in case we got one.
     *
     * @param ezcDocumentRstNode $node
     * @return void
     */
    protected function reduceLink( ezcDocumentRstNode $node )
    {
        if ( !isset( $this->documentStack[0] ) )
        {
            // This should never happen, though.
            return;
        }

        // Check a special case for anonymous hyperlinks, that the beforelast
        // token is not a '__'.
        if ( isset( $this->documentStack[1] ) &&
             ( $this->documentStack[1]->token->content === '__' ) )
        {
            return new ezcDocumentRstTextLineNode( $node->token );
        }

        // Aggregate token to check for an embedded inline URL
        $text = false;
        if ( ( $this->documentStack[0]->type === ezcDocumentRstNode::MARKUP_INTERPRETED ) ||
             ( $this->documentStack[0]->type === ezcDocumentRstNode::MARKUP_SUBSTITUTION ) )
        {
            /* DEBUG
            echo "   - Scan literal for embedded URI.\n";
            // /DEBUG */
            $textNode = false;
            $text = $this->documentStack->shift();
            foreach ( $text->nodes as $child )
            {
                if ( $child->type !== ezcDocumentRstNode::TEXT_LINE )
                {
                    /* DEBUG
                    echo "     - Invalid subnode type found: " . ezcDocumentRstNode::getTokenName( $child->type ) .  ".\n";
                    // /DEBUG */
                    $textNode = false;
                    break;
                }
                else
                {
                    $textNode .= $child->token->content;
                }
            }

            // If we could aggregate the texts easily, because there is only
            // plain text included, we can check for an URL.
            if ( ( $textNode !== false ) &&
                 ( preg_match( '(\s*<(?P<url>[^>]+)>)', $textNode, $match ) ) )
            {
                // We found an URL - remove it from the text and use it as link
                // target.
                /* DEBUG
                echo "   - Matched embedded URI: " . $match['url'] . ".\n";
                // /DEBUG */
                $text = new ezcDocumentRstTextLineNode( $text->nodes[0]->token );
                $text->token->content = str_replace( $match[0], '', $textNode );
                $node->target = $match['url'];
            }

            $text = array( $text );
        }
        elseif ( ( $this->documentStack[0]->type === ezcDocumentRstNode::TEXT_LINE ) &&
                 ( strpos( $this->documentStack[0]->token->content, ' ' ) === false ) )
        {
            /* DEBUG
            echo "   - Spaceless text found, aggregating:\n";
            // /DEBUG */
            $text = array();
            do {
                $text[] = $this->documentStack->shift();
            } while ( isset( $this->documentStack[0] ) &&
                      ( $this->documentStack[0]->type === ezcDocumentRstNode::TEXT_LINE ) &&
                      ( ( $this->documentStack[0]->token->type === ezcDocumentRstToken::TEXT_LINE ) ||
                        ( ( $this->documentStack[0]->token->type === ezcDocumentRstToken::SPECIAL_CHARS ) &&
                          ( preg_match( '(^[_-]+$)', $this->documentStack[0]->token->content ) ) ) ) &&
                      ( strpos( $this->documentStack[0]->token->content, ' ' ) === false ) );

            // Split away everything after the first space, and include it in
            // the reference target, if applicable
            if ( isset( $this->documentStack[0] ) &&
                 ( $this->documentStack[0]->type === ezcDocumentRstNode::TEXT_LINE ) &&
                 ( preg_match( '(^(?P<before>.*?)(?P<text>[A-Za-z0-9_-]+)$)s', $this->documentStack[0]->token->content, $match ) ) )
            {
                /* DEBUG
                echo "     - Splitting node: '{$this->documentStack[0]->token->content}' at $pos.\n";
                // /DEBUG */
                $token = clone $this->documentStack[0]->token;
                $this->documentStack[0]->token->content = rtrim( $match['before'] );
                $token->content = $match['text'];
                $text[] = new ezcDocumentRstTextLineNode( $token );
            }

            $text = array_reverse( $text );
        }

        if ( $text !== false )
        {
            /* DEBUG
            echo "   - Return created node.\n";
            // /DEBUG */
            $node->nodes = $text;
            return $node;
        }

        // If the single text node contains spaces we need to split it up here,
        // because the link only covers the last word in the string.
        if ( $this->documentStack[0]->type === ezcDocumentRstNode::TEXT_LINE )
        {
            // This is bit hackish, but otherwise the tokenizer would produce
            // too large amounts of structs.
            $words = explode( ' ', $this->documentStack[0]->token->content );
            $this->documentStack[0]->token->content = implode( ' ', array_slice( $words, 0, -1 ) ) . ' ';

            $token = clone $this->documentStack[0]->token;
            $token->content = end( $words );
            $text = new ezcDocumentRstTextLineNode( $token );

            $node->nodes = array( $text );
            return $node;
        }

        // We did not find a valid precedessor, so just convert to a text node.
        return new ezcDocumentRstTextLineNode( $node->token );
    }
}

?>
