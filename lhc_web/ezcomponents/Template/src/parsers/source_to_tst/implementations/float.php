<?php
/**
 * File containing the ezcTemplateFloatSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for float types.
 *
 * Floats are defined in the same way as in PHP.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateFloatSourceToTstParser extends ezcTemplateLiteralSourceToTstParser
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
     * Parses the float types by looking for float expression.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            $matches = $cursor->pregMatch( "#^(?:[0-9]+(([eE][+-]?[0-9]+)|((\\.[0-9]+)([eE][+-]?[0-9]+)?)))#" );
            if ( $matches !== false )
            {
                $float = new ezcTemplateLiteralTstNode( $this->parser->source, $this->startCursor, $cursor );
                $float->value = (float)$matches;
                $this->value = $float->value;
                $this->element = $float;
                $this->appendElement( $float );
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
        return "float";
    }
}

?>
