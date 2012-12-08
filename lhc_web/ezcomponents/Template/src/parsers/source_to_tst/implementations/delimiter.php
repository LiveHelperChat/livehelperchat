<?php
/**
 * File containing the ezcTemplateDelimiterSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for {delimiter}.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDelimiterSourceToTstParser extends ezcTemplateSourceToTstParser
{
    /**
     * Passes control to parent.
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
        // handle closing block
        if ( $this->block->isClosingBlock )
        {
            $this->findNextElement();
            if ( !$this->parentParser->atEnd( $cursor, null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor,  ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $cursor->advance();

            $el = new ezcTemplateDelimiterTstNode( $this->parser->source, $this->startCursor, $cursor );
            $el->isClosingBlock = true;
            $this->appendElement( $el );
            return true;
        }

        // handle opening block
        if ( $this->block->name == "delimiter" )
        {
            $delimiter = new ezcTemplateDelimiterTstNode( $this->parser->source, $this->startCursor, $cursor );
            $this->findNextElement();
            if ( $this->currentCursor->match( "modulo" ) )
            {
                $this->findNextElement();

                if ( !$this->parseOptionalType( 'Expression', null, false ) )
                {
                    throw new ezcTemplateSourceToTstParserException( $this, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
                }

                if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
                }

                $delimiter->modulo = $this->lastParser->rootOperator;

                if ( $this->currentCursor->match( "is" ) )
                {
                    $this->findNextElement();
                    if ( !$this->parseOptionalType( 'Expression', null, false ) )
                    {
                        throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
                    }
                        
                    if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
                    {
                        throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
                    }

                    $delimiter->rest = $this->lastParser->rootOperator;
                    $this->findNextElement();
                }
                else
                {
                    $delimiter->rest = new ezcTemplateLiteralTstNode( $this->parser->source, $this->startCursor, $this->endCursor );
                    $delimiter->rest->value = 0;
                }
            }


            $this->appendElement( $delimiter );


            if ( !$this->parentParser->atEnd( $cursor, null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                    ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $cursor->advance();
            return true;
        }
        elseif ( $this->block->name == "skip" )
        {
            $skip = new ezcTemplateLoopTstNode( $this->parser->source, $this->startCursor, $cursor, "skip" );

            $this->findNextElement();

            if ( !$this->parentParser->atEnd( $cursor, null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $cursor->advance();
            $this->appendElement( $skip );
            return true;
        }

        return false;
    }
}

?>
