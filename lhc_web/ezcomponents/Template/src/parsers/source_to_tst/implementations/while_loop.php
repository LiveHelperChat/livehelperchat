<?php
/**
 * File containing the ezcTemplateWhileLoopSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for while loops.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateWhileLoopSourceToTstParser extends ezcTemplateSourceToTstParser
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

        // skip whitespace and comments
        $this->findNextElement();

        if ( !$this->block->isClosingBlock )
        {
            if ( !$this->parseRequiredType( 'Expression', null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
            }

            if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
            }

            $condition = $this->lastParser->rootOperator;
        }

        if ( !$this->parentParser->atEnd( $cursor, null, false ) )
        {
            throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $cursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
        }

        // Everything went well. Let's create the element.
        $cursor->advance();

        $el = new ezcTemplateWhileLoopTstNode( $this->parser->source, $this->startCursor, $cursor );
        $el->name = $name;
        if ( isset( $condition ) )
            $el->condition = $condition;
        $el->isClosingBlock = $this->block->isClosingBlock;
        $this->appendElement( $el );

        return true;
    }
}

?>
