<?php
/**
 * File containing the ezcTemplateIfConditionSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for {if} control structure.
 *
 * Parses inside the blocks {...} and looks for an expression by using the
 * ezcTemplateExpressionSourceToTstParser class.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIfConditionSourceToTstParser extends ezcTemplateSourceToTstParser
{
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
        $this->block = null;
    }

    /**
     * Parses the expression by using the ezcTemplateExpressionSourceToTstParser class.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $name = $this->block->name;

        // handle closing block
        if ( $this->block->isClosingBlock )
        {
            // skip whitespace and comments
            $this->findNextElement();
            
            if ( !$cursor->match( '}' ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $el = new ezcTemplateIfConditionTstNode( $this->parser->source, $this->startCursor, $cursor );
            $el->name = 'if';
            $el->isClosingBlock = true;
            $this->appendElement( $el );
            return true;
        }

        $condition = null;

        $this->findNextElement();

        if ( $name != 'else' ) // Parse condition
        {
            if ( !$this->parseRequiredType( 'Expression', null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
            }

            $condition = $this->lastParser->rootOperator;
            if ( $condition instanceof ezcTemplateModifyingOperatorTstNode )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
            }

            $this->findNextElement();
        }

        if ( !$cursor->match( '}' ) )
        {
            throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
        }

        $cb = new ezcTemplateConditionBodyTstNode( $this->parser->source, $this->startCursor, $cursor );
        $cb->condition = $condition;
        $cb->name = $name;

        if ( $name == 'if' )
        {
            $el = new ezcTemplateIfConditionTstNode( $this->parser->source, $this->startCursor, $cursor );
            $el->children[] = $cb;
            $el->name = 'if';
            $this->appendElement( $el );
        }
        else
        {
            $this->appendElement( $cb );
        }

        return true;
    }
}

?>
