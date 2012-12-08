<?php
/**
 * File containing the ezcTemplateStringSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for string types.
 *
 * Strings are defined in the same way as in PHP, however the double quoted
 * strings cannot have references to PHP variables inside them.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateStringSourceToTstParser extends ezcTemplateLiteralSourceToTstParser
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
     * Parses the string types by looking for single or double quotes to start
     * the string.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            $char = $cursor->current();
            if ( $char == '"' ||
                 $char == "'" )
            {
                $string = new ezcTemplateLiteralTstNode( $this->parser->source, $this->startCursor, $cursor );
                $string->quoteType = ( $char == "'" ? ezcTemplateLiteralTstNode::SINGLE_QUOTE : ezcTemplateLiteralTstNode::DOUBLE_QUOTE );

                $cursor->advance();

                $nextChar = $cursor->current();
                if ( $nextChar === $char )
                {
                    // We know it is an empty string, no need to extract
                    $str = "";
                    $string->value = $str;
                    $this->value = $string->value;
                    $this->element = $string;
                    $this->appendElement( $string );
                    $cursor->advance();
                    return true;
                }
                else
                {
                    // Match: 
                    // ([^{$char}\\\\]|\A)   : Matches non quote ('"', "'"), non backslash (\), or does match the begin of the statement. 
                    // (\\\\(\\\\|{$char}))* : Eat double slashes \\ and slash quotes: \' or \". 

                    $matches = $cursor->pregMatchComplete( "#(?:([^{$char}\\\\]|\A)(\\\\(\\\\|{$char}))*){$char}#" );

                    if ( $matches === false )
                        return false;

                    $cursor->advance( $matches[0][1] + strlen( $matches[0][0] ) );
                    $str = (string)$this->startCursor->subString( $cursor->position );
                    $str = substr( $str, 1, -1 );

                    $string->value = $str;
                    $this->value = $string->value;
                    $this->element = $string;
                    $this->appendElement( $string );
                    return true;
                }
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
        return "string";
    }
}

?>
