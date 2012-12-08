<?php
/**
 * File containing the ezcTemplateLoopSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for {break}, {continue}, etc
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateLoopSourceToTstParser extends ezcTemplateSourceToTstParser
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
        $element = new ezcTemplateLoopTstNode( $this->parser->source, $this->startCursor, $cursor, $this->block->name );
        if ( $this->block->isClosingBlock )
        {
            $element->isClosingBlock = true;
        }

        $this->findNextElement();

        if ( !$cursor->match( "}") )
        {
            throw new ezcTemplateParserException( $this->parser->source, $cursor, $cursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
        }

        $this->appendElement( $element );

        return true;
    }
}

?>
