<?php
/**
 * File containing the ezcTemplateCustomBlockSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for custom template blocks following a generic syntax.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCustomBlockSourceToTstParser extends ezcTemplateSourceToTstParser
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
     * Returns the custom block definition.
     *
     * @param string $name
     * @return ezcTemplateCustomBlockDefinition
     */
    function getCustomBlockDefinition( $name )
    {
        foreach ( $this->parser->template->configuration->customBlocks as $class )
        {
            $def = call_user_func( array( $class, "getCustomBlockDefinition" ),  $name );

            if ( $def instanceof ezcTemplateCustomBlockDefinition )
            {
                return $def;
            }
        }

        return false;
    }


    /**
     * Parses the expression by using the ezcTemplateExpressionSourceToTstParser class.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( $this->block->isClosingBlock )
        {
            $this->findNextElement();

            $matches = $cursor->pregMatchComplete( "#^([a-zA-Z_][a-zA-Z0-9_-]*)(?:[^a-zA-Z])#i" );
            if ( $matches === false )
                return false;

            $name = $matches[1][0];
            // If the custom block is not defined we have to return false.
            $def = $this->getCustomBlockDefinition( $name );
            if ( $def === false )
            {
                return false;
            }

            $cursor->advance( strlen( $name ) );
            $this->findNextElement( $cursor );

            if ( !$cursor->match( "}" ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $cb = new ezcTemplateCustomBlockTstNode( $this->parser->source, $this->startCursor, $cursor );

            $cb->name = $name;
            $cb->isClosingBlock = true;

            $this->appendElement( $cb );
            return true;
        }
 
        // Check for the name of the custom block
        // Note: The code inside the ( ?: ) brace ensures that the next character
        // is not an alphabetical character ie. a word boundary
        $matches = $cursor->pregMatchComplete( "#^([a-zA-Z_][a-zA-Z0-9_-]*)(?:[^a-zA-Z])#i" );
        if ( $matches === false )
        {
            return false;
        }
       
        $name = $matches[1][0];

        $cursor->advance( strlen( $name ) );
        $this->findNextElement( $cursor );

        // $def = ezcTemplateCustomBlockManager::getInstance()->getDefinition( $name );

        $def = $this->getCustomBlockDefinition( $name );

        if ( $def === false )
        {
            return false;
        }

        $cb = new ezcTemplateCustomBlockTstNode( $this->parser->source, $this->startCursor, $cursor );
        $cb->definition = $def;
        $cb->name = $name;
        $this->block->isNestingBlock = $cb->isNestingBlock = $def->hasCloseTag;

        $excessParameters = isset( $def->excessParameters ) && $def->excessParameters ? true : false;  

        if ( isset( $def->startExpressionName ) && $def->startExpressionName != "" )
        {
            if ( !in_array( $def->startExpressionName, $def->optionalParameters ) && !in_array( $def->startExpressionName, $def->requiredParameters ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                    sprintf( ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_REQUIRED_OR_OPTIONAL_PARAMETER_DEFINITION_IN_CUSTOM_BLOCK, $def->startExpressionName ) );
            }

            if ( !$this->parseOptionalType( 'Expression', null, false ) )
            {
                if ( in_array( $def->startExpressionName, $def->requiredParameters ) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
                }
            }
            else
            {
                if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
                }

                $cb->namedParameters[ $def->startExpressionName ] = $this->lastParser->rootOperator;
                $this->findNextElement( $cursor );
            }
        }

        while ( !$cursor->match( "}" ) )
        {
            $match = $cursor->pregMatch( "#^[a-zA-Z_][a-zA-Z0-9_-]*#");
            if ( !$match )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                   sprintf(  ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_TOKEN, $cursor->current( 1 ) ) );
 
            }

            if ( !$excessParameters && !in_array( $match, $def->optionalParameters ) && !in_array( $match, $def->requiredParameters ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                    sprintf(  ezcTemplateSourceToTstErrorMessages::MSG_UNKNOWN_CUSTOM_BLOCK_PARAMETER, $match) );
            }

            if ( array_key_exists( $match, $cb->namedParameters )  )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                    sprintf( ezcTemplateSourceToTstErrorMessages::MSG_REASSIGNMENT_CUSTOM_BLOCK_PARAMETER, $match ) );
            }

            $this->findNextElement( $cursor );
            // The '=' is optional.
            if ( $cursor->match( "=" ) )
            {
                $this->findNextElement( $cursor );
            }

            // The parameter has an expression.
            if ( !$this->parseOptionalType( 'Expression', null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
            }

            if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
            }

 
            // Append the parameter to the "namedParameters" array.
            $cb->namedParameters[ $match ] = $this->lastParser->rootOperator;
        }

        // Check if all requiredParameters are set.
        foreach ( $def->requiredParameters as  $val )
        {
            if ( !array_key_exists( $val, $cb->namedParameters) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                    sprintf(  ezcTemplateSourceToTstErrorMessages::MSG_MISSING_CUSTOM_BLOCK_PARAMETER, $val ) );
            }
        }

        $this->appendElement( $cb );

        return true;
    }
}

?>
