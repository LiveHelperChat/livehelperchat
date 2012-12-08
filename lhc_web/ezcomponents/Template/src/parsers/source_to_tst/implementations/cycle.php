<?php
/**
 * File containing the ezcTemplateCycleSourceToTstParser class
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
class ezcTemplateCycleSourceToTstParser extends ezcTemplateSourceToTstParser
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
        if ( $this->block->name == "increment" || $this->block->name == "decrement" || $this->block->name == "reset" )
        {
            $cycle = new ezcTemplateCycleControlTstNode( $this->parser->source, $this->startCursor, $cursor, $this->block->name );

            do
            {
                $this->findNextElement();
                
                if ( !$this->parseOptionalType( "Variable", null, false) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_VARIABLE );
                }

                $cycle->variables[] = $this->lastParser->elements[0];

                $this->findNextElement();

            }
            while ( $this->currentCursor->match( "," ) );

            $this->appendElement( $cycle );
            // $this->elements[0] = $cycle; // Replace the variable, with the cycle.


            if ( !$this->parentParser->atEnd( $cursor, null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }
            $cursor->advance();

            return true;
        }



        return false;
    }
}

?>
