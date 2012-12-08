<?php
/**
 * File containing the ezcTemplateLiteralSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for all builtin types.
 *
 * Literal types are parsed by utilizing the various sub-parser for known
 * types.
 *
 * Once the type has been parsed it can be fetched by using the
 * property $value for the value and $element for the element object.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateLiteralSourceToTstParser extends ezcTemplateSourceToTstParser
{
    /**
     * The value of the parsed type or null if nothing was parsed.
     * @var mixed
     */
    public $value;

    /**
     * The parsed element object which defines the type or null if nothing
     * was parsed.
     *
     * @var ezcTemplateTstNode
     */
    public $element;

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
        $this->value = null;
        $this->element = null;
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
        $failedParser = null;
        if ( !$cursor->atEnd() )
        {
            // Try parsing the various type types until one is found
            $failedCursor = clone $cursor;

            $types = array( 'Float', 'Integer', 'String', 'Bool', 'Array', 'Null' );
            foreach ( $types as $type )
            {
                if ( $this->parseOptionalType( $type ) )
                {
                    $this->lastCursor->copy( $this->startCursor );
                    $this->value = $this->lastParser->value;
                    $this->element = $this->lastParser->element;
                    return true;
                }
            }
        }
        return false;
    }
}

?>
