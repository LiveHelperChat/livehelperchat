<?php
/**
 * File containing the ezcTemplateFunctionCallSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for function calls.
 *
 * An calls looks like:
 * <code>
 * <function name> LEFT_PARENTHESIS <parameter 1> [, <parameter 2> ...] RIGHT_PARENTHESIS
 * e.g.
 * str_replace( " ", "_", $str )
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateFunctionCallSourceToTstParser extends ezcTemplateSourceToTstParser
{

    /**
     * The function call object if the parser was succesful.
     * @var ezcTemplateFunctionCallTstNode
     */
    public $functionCall;

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
        $this->readingParameter = false;
    }

    /**
     * Figures out if the end as been reached and returns true if it has.
     *
     * The end is reached when it finds the character ].
     *
     * @param ezcTemplateCursor $cursor
     * @param ezcTemplateTstNode $operator  
     * @param bool $finalize
     * @return bool
     */
    public function atEnd( ezcTemplateCursor $cursor, /*ezcTemplateTstNode*/ $operator, $finalize = true )
    {
        if ( $cursor->current() == ')' )
        {
            return true;
        }
        else if ( $this->readingParameter && $cursor->current() == ',' )
        {
            return true;
        }
        return false;
    }

    /**
     * Parses the function call and the parameters, the parameters are parsed
     * using the generic expression parser.
     * The expression will callback the atEnd() function to figure out if the
     * end is reached or not.
     *
     * Look ahead: Identifier '('
     * Complete  : Identifier '(' ( Parameter ( ',' Parameter )* )? ')'
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $failedParser = null;

        // $cursor will be update as the parser continues
        $this->functionCall = new ezcTemplateFunctionCallTstNode( $this->parser->source, $this->startCursor, $cursor );

        // skip whitespace and comments
        if ( !$this->findNextElement() )
        {
            return false;
        }

        $this->functionCall->name = $cursor->pregMatch( "#^[a-zA-Z_][a-zA-Z0-9_]*#" );
        if ( $this->functionCall->name === false )
        {
            return false;
        }

        $this->findNextElement();

        if ( !$cursor->match( '(' ) )
        {
            return false;
        }

        $this->findNextElement();

        $this->parameterCount = 1;

        if ( !$this->parseParameter( $cursor ) )
        {
            $this->findNextElement();
            if ( !$cursor->match( ')' ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_ROUND_BRACKET_CLOSE );
            }

            $this->functionCall->endCursor = clone $cursor;
            $this->appendElement( $this->functionCall );
            return true;
        }

        while ( true )
        {
            $this->findNextElement();

            if ( $cursor->match( ')' ) )
            {
                $this->functionCall->endCursor = clone $cursor;
                $this->appendElement( $this->functionCall );
                return true;
            }

            if ( !$cursor->match( ',' ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_ROUND_BRACKET_CLOSE_OR_COMMA );
            }

            $this->findNextElement();

            ++$this->parameterCount;

            // $this->operationState = false;

            if ( !$this->parseParameter( $cursor ) )
            {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor,  sprintf( ezcTemplateSourceToTstErrorMessages::MSG_PARAMETER_EXPECTS_EXPRESSION, $this->parameterCount ) );
            }

        }
    }
    
    /**
     * Parse a parameter
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseParameter( $cursor )
    {
        // Without this, the expression parser keeps on reading.
        if ( $cursor->match( ')', false ) )
        {
            return false;
        }

        $this->readingParameter = true;
        
        
        $startCursor = clone $cursor;
        $namedParameter = $cursor->pregMatch( "#^[a-zA-Z_][a-zA-Z0-9_]*#" );
        
        if ( $namedParameter !== false )
        {
           $this->findNextElement();

           if ( !$cursor->match("=") )
           {
               $namedParameter = false;
           }
        }

        if ( $namedParameter === false)
        {
            $cursor->copy($startCursor);
        }
        
        // Check for expression, the parser will call self::atEnd() to check for end of expression.
        $expressionStartCursor = clone $cursor;
        $expressionParser = new ezcTemplateExpressionSourceToTstParser( $this->parser, $this, null );
        $expressionParser->allowIdentifier = true;
        if ( !$this->parseRequiredType( $expressionParser ) || $this->lastParser->currentOperator === null )
        {
            return false;
        }

        $rootOperator = $this->lastParser->currentOperator;

        if ( $rootOperator instanceof ezcTemplateOperatorTstNode )
        {
            $rootOperator = $rootOperator->getRoot();
        }

        if ( $rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
        {
            throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor,  sprintf( ezcTemplateSourceToTstErrorMessages::MSG_PARAMETER_CANNOT_BE_MODIFYING_BLOCK, $this->parameterCount ) );
        }

        if ( $namedParameter !== false )
        {
            if ( isset( $this->functionCall->parameters[$namedParameter] ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, sprintf(ezcTemplateSourceToTstErrorMessages::MSG_NAMED_PARAMETER_ALREADY_ASSIGNED, $namedParameter ) );
            }

            $this->functionCall->parameters[$namedParameter] = $rootOperator;

        }
        else
        {
            $this->functionCall->appendParameter( $rootOperator );
        }

        $this->readingParameter = false;
        return true;
    }
}

?>
