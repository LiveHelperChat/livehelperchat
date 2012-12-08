<?php
/**
 * File containing the ezcTemplateBoolSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for boolean types.
 *
 * Booleans are defined in the same way as in PHP.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBoolSourceToTstParser extends ezcTemplateLiteralSourceToTstParser
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
     * Parses the boolean types by looking for either 'true' or 'false'.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            // @todo This should check that there is no alphabetical characters
            //       after the true|false.
            $matches = $cursor->pregMatchComplete( "#^(true|false)(?:\W)#i" );
            if ( $matches === false )
                return false;

            $name = $matches[1][0];

            $lower = strtolower( $name );
            if ( $name !== $lower )
            {
                $this->findNonLowercase();
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_BOOLEAN_NOT_LOWERCASE );
            }

            $cursor->advance( strlen( $name ) );
            $bool = new ezcTemplateLiteralTstNode( $this->parser->source, $this->startCursor, $cursor );
            $bool->value = $name == 'true';
            $this->value = $bool->value;
            $this->element = $bool;
            $this->appendElement( $bool );
            return true;
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
        return "boolean";
    }
}

?>
