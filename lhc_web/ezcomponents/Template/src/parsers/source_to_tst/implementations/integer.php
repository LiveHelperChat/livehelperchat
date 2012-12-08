<?php
/**
 * File containing the ezcTemplateIntegerSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for integer types.
 *
 * Integers are defined in the same way as in PHP.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIntegerSourceToTstParser extends ezcTemplateLiteralSourceToTstParser
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
     * Parses the integer types by looking for numerical characters.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            $matches = $cursor->pregMatch( "#^-?[0-9]+#" );
            if ( $matches !== false )
            {
                $integer = new ezcTemplateLiteralTstNode( $this->parser->source, $this->startCursor, $cursor );
                $integer->value = (int)$matches;
                $this->value = $integer->value;
                $this->element = $integer;
                $this->appendElement( $integer );
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
        return "integer";
    }
}

?>
