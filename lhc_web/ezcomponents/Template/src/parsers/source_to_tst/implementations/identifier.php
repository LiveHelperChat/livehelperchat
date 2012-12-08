<?php
/**
 * File containing the ezcTemplateIdentifierSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for identifier types.
 *
 * Identifiers consists of a-z, A-Z, underscore (_) and numbers only.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIdentifierSourceToTstParser extends ezcTemplateLiteralSourceToTstParser
{
    /**
     * The identifier which was found while parsing or null if no identifier
     * has been found yet.
     *
     * @var string
     */
    public $identifierName;

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
        $this->identifierName = null;
    }

    /**
     * Parses the identifier types by looking for allowed characters.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            $matches = $cursor->pregMatch( "#^[a-zA-Z_][a-zA-Z0-9_]*#" );
            if ( $matches !== false )
            {
                $identifier = new ezcTemplateIdentifierTstNode( $this->parser->source, $this->startCursor, $cursor );
                $identifier->value = (string)$matches;
                $this->identifierName = $identifier->value;
                $this->element = $identifier;
                $this->appendElement( $identifier );
                return true;
            }
        }
        return false;
    }
}

?>
