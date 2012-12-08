<?php
/**
 * File containing the ezcTemplateSwitchConditionSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateSwitchConditionSourceToTstParser extends ezcTemplateSourceToTstParser
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

            if ( $name == 'switch' )
            {
                $sw = new ezcTemplateSwitchTstNode( $this->parser->source, $this->startCursor, $cursor );
            }
            else
            {
                // Tricky: Skip the spaces and new lines. Next element should be an case, or default.
                // $this->findNextElement();

                $sw = new ezcTemplateCaseTstNode( $this->parser->source, $this->startCursor, $cursor );
                $sw->name = $name; // Set the name to either 'case' or 'default'.
            }
            // $el->name = 'switch';
            $sw->isClosingBlock = true;
            $this->appendElement( $sw );
            return true;
        }


        if ( $name == 'switch' )
        {
            $this->findNextElement();
            if ( !$this->parseRequiredType( 'Expression', null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
            }

            if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
            }


            $this->findNextElement();
            if ( !$cursor->match( '}' ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            // Tricky: Skip the spaces and new lines. Next element should be an case, or default.
            // $this->findNextElement();

            $sw = new ezcTemplateSwitchTstNode( $this->parser->source, $this->startCursor, $cursor );
            $sw->condition = $this->lastParser->rootOperator;
            $this->appendElement( $sw );

            return true;
        }
        elseif ( $name == 'case' )
        {
            $case = new ezcTemplateCaseTstNode( $this->parser->source, $this->startCursor, $cursor );
            $case->name = $name; // Set the name to 'case'

            do
            {
                $this->findNextElement();

                if ( !$this->parseRequiredType( 'Literal', null, false ) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_LITERAL );
                }

                $case->conditions[] = $this->lastParser->element;

                $this->findNextElement();

            } 
            while ( $cursor->match( ',' ) );

            if ( !$cursor->match( '}' ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            // Tricky: Skip the spaces and new lines. Next element should be an case, or default.
            $this->findNextElement();


            $this->appendElement( $case );

            return true;
        }
        elseif ( $name == 'default' )
        {
            $case = new ezcTemplateCaseTstNode( $this->parser->source, $this->startCursor, $cursor );
            $case->name = $name; // Set the name to 'default'
            $case->conditions = null;
            $this->findNextElement();

            if ( !$cursor->match( '}' ) )
            {
                throw new ezcTemplateSourceToTstParserException( $this, $cursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            // Tricky: Skip the spaces and new lines. Next element should be an case, or default.
            $this->findNextElement();
            $this->appendElement( $case );

            return true;
        }
    }
}

?>
