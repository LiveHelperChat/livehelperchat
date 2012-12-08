<?php
/**
 * File containing the ezcTemplateBlockCommentSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for block comments.
 *
 * Block comments start with a slash (/) and an asterix (*) and ends with an
 * asterix (*) and a slash (/).
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBlockCommentSourceToTstParser extends ezcTemplateSourceToTstParser
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
     * Parses the comment by looking for the end marker * + /.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( !$cursor->atEnd() )
        {
            $cursor->advance( 2 );

            $tagPos = $cursor->findPosition( '*/' );
            if ( $tagPos !== false )
            {
                // reached end of comment
                $cursor->gotoPosition( $tagPos + 2 );
                $commentBlock = new ezcTemplateBlockCommentTstNode( $this->parser->source, $this->startCursor, clone $cursor );

                $commentBlock->commentText = substr( $commentBlock->text(), 2, -2 );
                $this->appendElement( $commentBlock );
                return true;
            }
        }
        return false;
    }
}

?>
