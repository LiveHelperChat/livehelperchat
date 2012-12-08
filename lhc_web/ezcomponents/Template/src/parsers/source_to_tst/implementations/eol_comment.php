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
 * Parser for end-of-line comments.
 *
 * EOL comments start with a double slash (//) and goes on until the end of the
 * line.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateEolCommentSourceToTstParser extends ezcTemplateSourceToTstParser
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
     * Parses the comment by looking for the end marker \n.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $cutOff = false;
        if ( !$cursor->atEnd() )
        {
            $cursor->advance( 2 );

            $matches = $cursor->pregMatchComplete( "#^([^}\r\n]*)(?:(?:})|(\r|\r\n|\n))#" );
            if ( $matches )
            {
                // reached end of comment
                $cutOff = false;
                if ( isset( $matches[2] ) )
                {
                    $cursor->advance( $matches[2][1] + strlen( $matches[2][0] ) );
                    // Do not include the newline itself in the comment.
                    $cutOff = -1;
                }
                else
                {
                    $cursor->advance( $matches[1][1] + strlen( $matches[1][0] ) );
                }
            }
            else
            {
                $cursor->gotoEnd();
            }
            $commentBlock = new ezcTemplateEolCommentTstNode( $this->parser->source, $this->startCursor, clone $cursor );

            if ( $cutOff )
            {
                $commentBlock->commentText = substr( $commentBlock->text(), 2, $cutOff );
            }
            else
            {
                $commentBlock->commentText = substr( $commentBlock->text(), 2 );
            }
            $this->appendElement( $commentBlock );
            return true;
        }
        return false;
    }
}

?>
