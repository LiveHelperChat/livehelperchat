<?php
/**
 * File containing the ezcTemplateNullSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for null types.
 *
 * Floats are defined in the same way as in PHP.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateNullSourceToTstParser extends ezcTemplateLiteralSourceToTstParser
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
     * Parses the null type.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            if ( $cursor->match( "null" ) )
            {
                $literal = new ezcTemplateLiteralTstNode( $this->parser->source, $this->startCursor, $cursor );
                $literal->value = null;
                $this->element = $literal;
                $this->appendElement( $literal );
                return true;
            } 
       }
       return false;
    }
 
    /**
     * Returns a string representing the current type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return "null";
    }
}

?>
