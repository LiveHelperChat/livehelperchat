<?php
/**
 * File containing the ezcTemplateDeclarationSourceToTstParser class
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
class ezcTemplateDeclarationBlockSourceToTstParser extends ezcTemplateSourceToTstParser
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
    }

    /**
     * Parses the types by utilizing:
     * - ezcTemplateFloatSourceToTstParser for float types.
     * - ezcTemplateIntegerSourceToTstParser for integer types.
     * - ezcTemplateStringSourceToTstParser for string types.
     * - ezcTemplateBoolSourceToTstParser for boolean types.
     * - ezcTemplateArraySourceToTstParser for array types.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool 
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $this->findNextElement();
        $symbolType = null;

        $matches = $this->currentCursor->pregMatchComplete( "#^(var|cycle|use)(?:[^a-zA-Z0-9_])#" );
        $name = $matches[1][0];
        if ( $name == "var" )       $symbolType = ezcTemplateSymbolTable::VARIABLE;
        elseif ( $name == "cycle" ) $symbolType = ezcTemplateSymbolTable::CYCLE;
        elseif ( $name == "use" )   $symbolType = ezcTemplateSymbolTable::IMPORT;

        if ( $symbolType !== null )
        {
            $this->currentCursor->advance( strlen( $name ) );
            $this->findNextElement();

            // $var
            if ( $this->parseSubDefineBlock( $symbolType ) )
            {
                if ( $this->currentCursor->match( "}" ) )
                {
                    return true;
                }
                elseif ( $this->currentCursor->match( ":", false ) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                        sprintf( ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_TOKEN, $this->currentCursor->current( 1 ) ), ezcTemplateSourceToTstErrorMessages::LNG_INVALID_NAMESPACE_MARKER ); 
                }
                else
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                        sprintf( ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_TOKEN, $this->currentCursor->current( 1 ) ) );
                }
            }
            else
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_VARIABLE );
            }
        }

        return false;
    }

    /**
     * Returns true if the current character is a curly bracket (}) which means
     * the end of the block.
     *
     * @todo Can be removed?
     *
     * @param ezcTemplateCursor $cursor
     * @param ezcTemplateTstNode $operator  
     * @param bool $finalize
     * @return bool
     */
    public function atEnd( ezcTemplateCursor $cursor, /*ezcTemplateTstNode*/ $operator, $finalize = true )
    {
        return ( $cursor->current( 1 ) == "}"  || $cursor->current( 1 ) == "," );
    }

    /**
     * Parses the define block <var> = <expr>. 
     *
     * @param int $symbolType   Has one of the values:  ezcTemplateSymbolTable::VARIABLE,  ezcTemplateSymbolTable::CYCLE, 
     *                        ezcTemplateSymbolTable::IMPORT.
     * @return bool
     */
    protected function parseSubDefineBlock( $symbolType )
    {
        $isFirst = true; // First Variable parse, may be invalid. Return false in that case. 

        do
        {
            $this->findNextElement();

            $declaration = new ezcTemplateDeclarationTstNode( $this->parser->source, $this->startCursor, $this->currentCursor );
            $declaration->isClosingBlock = false;
            $declaration->isNestingBlock = false;

            if ( ! $this->parseOptionalType( "Variable", $this->currentCursor, false ) )
            {
                if ( $isFirst ) return false;

                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_VARIABLE );
            }

            $isFirst = false;
            $declaration->variable = $this->lastParser->elements[0];

            // Variable name.
            if ( !$this->parser->symbolTable->enter( $declaration->variable->name, $symbolType ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, $this->parser->symbolTable->getErrorMessage() );
            }

            $this->findNextElement();

            if ( $this->currentCursor->match( "=" ) )
            {
                $this->findNextElement();

                if ( !$this->parseOptionalType( "Expression", null, false ) )
                {
                    if ( $this->parseOptionalType( "Identifier", null, false ) )
                    {
                        throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION_NOT_IDENTIFIER );
                    }
                    else
                    {
                        throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_EXPRESSION );
                    }
                }

                if ( $this->lastParser->rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_MODIFYING_EXPRESSION_NOT_ALLOWED );
                }

                $declaration->expression = $this->lastParser->rootOperator;
            }

            $this->appendElement( $declaration );
            $this->findNextElement();

        } while ( $this->currentCursor->match( "," ) );

        return true;
   }
}

?>
