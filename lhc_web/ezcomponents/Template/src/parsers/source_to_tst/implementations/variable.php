<?php
/**
 * File containing the ezcTemplateVariableSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for variable definitions.
 *
 * Variables are defined in the same way as in PHP.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateVariableSourceToTstParser extends ezcTemplateSourceToTstParser
{
 
    /**
     * The variable name which was found while parsing or null if no variable
     * has been found yet.
     *
     * @var string
     */
    public $variableName;

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
        $this->variable = null;
        $this->variableName = null;
    }

    /**
     * Parses the variable types by looking for a dollar sign followed by an
     * identifier. The identifier is parsed by using ezcTemplateIdentifierSourceToTstParser.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            if ( $cursor->match( '$' ) )
            {
                if ( $cursor->current() == '#' )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                            ezcTemplateSourceToTstErrorMessages::MSG_INVALID_VARIABLE_NAME, ezcTemplateSourceToTstErrorMessages::LNG_INVALID_NAMESPACE_ROOT_MARKER );
                }

                if ( $cursor->current() == ':' )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                            ezcTemplateSourceToTstErrorMessages::MSG_INVALID_VARIABLE_NAME, ezcTemplateSourceToTstErrorMessages::LNG_INVALID_NAMESPACE_MARKER );
                }

                if ( !$this->parseRequiredType( 'Identifier', null, false ) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_INVALID_VARIABLE_NAME, ezcTemplateSourceToTstErrorMessages::MSG_INVALID_IDENTIFIER );

                    return false;
                }

                $this->variableName = $this->lastParser->identifierName;

                $variable = new ezcTemplateVariableTstNode( $this->parser->source, $this->startCursor, $cursor );
                $variable->name = $this->variableName;
                $this->element = $variable;
                $this->appendElement( $variable );
                return true;
            }
        }
        return false;
    }
}

?>
