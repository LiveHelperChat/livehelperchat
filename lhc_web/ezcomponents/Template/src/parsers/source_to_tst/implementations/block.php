<?php
/**
 * File containing the ezcTemplateBlockSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for template blocks.
 *
 * Parses inside the blocks {...} by utilizing other elements parsers such as
 * ezcTemplateBlockCommentSourceToTstParser, ezcTemplateEolCommentSourceToTstParser,
 * ezcTemplateDocCommentSourceToTstParser and ezcTemplateExpressionBlockSourceToTstParser
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBlockSourceToTstParser extends ezcTemplateSourceToTstParser
{
    /**
     * The parsed block code is not recognized as a valid block.
     */
    const STATE_UNKNOWN_BLOCK = 1;

    /**
     * Passes control to parent.
     *
     * @param ezcTemplateParser $parser
     * @param ezcTemplateSourceToTstParser $parentParser
     * @param ezcTemplateCursor $startCursor
     */
    function __construct( ezcTemplateParser $parser, /*ezcTemplateSourceToTstParser*/ $parentParser, /*ezcTemplateCursor*/ $startCursor )
    {
        parent::__construct( $parser, $parentParser, $startCursor );
    }

    /**
     * Parses the block by using sub parser, the conditions are:
     * - The block contains {*...*} in which case ezcTemplateDocCommentSourceToTstParser is
     *   used.
     * - The block contains a generic expression in which case
     *   ezcTemplateExpressionBlockSourceToTstParser is used.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        // Check for doc comments which look like {*...*}
        if ( !$cursor->atEnd() &&
             $cursor->current() == '*' )
        {
            // got a doc comment block
            if ( !$this->parseRequiredType( 'DocComment', $this->startCursor ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $cursor, $cursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CLOSING_BLOCK_COMMENT );
            }

            return true;
        }


        // $cursor object in $block will be updated as the parser continues
        $this->block = new ezcTemplateBlockTstNode( $this->parser->source, $this->startCursor, $cursor );
        $this->findNextElement();

        // Test for and ending control structure.
        if ( !$cursor->atEnd() && $cursor->current() == '/' )
        {
            // got a closing block marker
            $this->block->isClosingBlock = true;
            $closingCursor = clone $cursor;
            $this->block->closingCursor = $closingCursor;
            $cursor->advance( 1 );

            $this->findNextElement();

            // Check for internal blocks which are known to not support closing markers.
            // foreach|while|if|switch|case|default|delimiter|literal|dynamic|cache_template
            $matches = $cursor->pregMatchComplete( "#^(tr|tr_context|elseif|else|include|return|break|continue|skip|increment|decrement|reset|once|var|use|cycle|ldelim|rdelim)(?:[^a-zA-Z0-9_])#" );
            if ( $matches !== false )
            {
                throw new ezcTemplateParserException( $this->parser->source,
                                                      $this->block->closingCursor, $this->block->closingCursor,
                                                      ezcTemplateSourceToTstErrorMessages::MSG_CLOSING_BLOCK_NOW_ALLOWED );
            }
        }


        // Try to parse a control structure
        $controlStructureParser = new ezcTemplateControlStructureSourceToTstParser( $this->parser, $this, null );
        $controlStructureParser->block = $this->block;
        if ( $this->parseOptionalType( $controlStructureParser, null, false ) )
        {
            if ( $this->lastParser->status == self::PARSE_PARTIAL_SUCCESS )
            {
                return false;
            }

            $this->mergeElements( $this->lastParser );
            return true;
        }

        // Try to parse a literal block
        if ( $this->parseOptionalType( 'LiteralBlock', $this->startCursor ) )
        {
            return true;
        }

        // Try to parse a declaration block
        if ( $this->parseOptionalType( 'DeclarationBlock', $this->startCursor ) )
        {
            return true;
        }

        // Try to parse as an expression, if this fails the normal block parser
        // is tried.
        if ( $this->parseOptionalType( 'ExpressionBlock', $this->startCursor ) )
        {
            if ( !$this->currentCursor->match( '}' ) )
            {
                if ( $this->currentCursor->match( '[', false ) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_SQUARE_BRACKET_OPEN );
                }

                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            return true;
        }

        if ( $cursor->match( '}' ) )
        {
            // Empty block found, this is allowed but the returned block
            // will be ignored when compiling
            $this->elements[] = $this->lastParser->block;
            return true;
        }

        // Parse the {ldelim} and {rdelim}
        $matches = $this->currentCursor->pregMatchComplete( "#^(ldelim|rdelim)(?:[^a-zA-Z0-9_])#" );
        $name = $matches[1][0];
        $ldelim = $name == 'ldelim' ? true : false;
        $rdelim = $name == 'rdelim' ? true : false;
        if ( $ldelim || $rdelim )
        {
            $this->currentCursor->advance( strlen( $name ) );
            $this->findNextElement();
            if ( !$this->currentCursor->match( "}" ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $text = new ezcTemplateTextBlockTstNode( $this->parser->source, $this->startCursor, $this->endCursor );
            $text->text = $ldelim ? "{" : "}";
            $this->appendElement( $text );
            return true;
        }

        // Parse the cache blocks.
        $cacheParser = new ezcTemplateCacheSourceToTstParser( $this->parser, $this, null );
        $cacheParser->block = $this->block;
        if ( $this->parseOptionalType( $cacheParser, null ) )
        {
            return true;
        }
        

        // Try to parse custom blocks, these are pluggable and follows a generic syntax.
        $customBlockParser = new ezcTemplateCustomBlockSourceToTstParser( $this->parser, $this, null );
        $customBlockParser->block = $this->block;
        if ( $this->parseOptionalType( $customBlockParser, null ) )
        {
            return true;
        }

        $matches = $cursor->pregMatchComplete( "#^([a-zA-Z_][a-zA-Z0-9_-]*)(?:[^a-zA-Z])#i" );
        throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, sprintf( ezcTemplateSourceToTstErrorMessages::MSG_UNKNOWN_BLOCK, $matches[1][0] ) );
    }
}

?>
