<?php
/**
 * File containing the ezcTemplateDocCommentSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for doc comment blocks.
 *
 * Doc comments start with a curly bracket ({) and an asterix (*) and ends with
 * an asterix (*) and a curly bracket (}).
 * e.g.
 * <code>
 * {* This is a doc comment *}
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDocCommentSourceToTstParser extends ezcTemplateSourceToTstParser
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
     * Parses the comment by looking for the end marker * + }.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $cursor->advance();
        if ( $cursor->atEnd() )
            return false;

        $checkInlineComment = false;
        // Check for a slash after the asterix, this typically means a typo for an inline comment
        // Better give an error for this to warn the user.
        if ( $cursor->current() == '/' )
        {
            $checkInlineComment = true;
        }

        $endPosition = $cursor->findPosition( '*}' );
        if ( $endPosition === false )
        {
            return false;
        }
        
        // If we found an end for an inline comment we need to check if there
        // is an end for an inline comment
        if ( $checkInlineComment )
        {
            $commentCursor = $cursor->cursorAt( $cursor->position, $endPosition );
            $commentCursor->advance();
            $inlineCommentPosition = $commentCursor->findPosition( '*/' );
            // We found the end of the inline comment, this is most likely a user error
            if ( $inlineCommentPosition !== false )
            {
                $cursor->gotoPosition( $inlineCommentPosition );
                return false;
            }
        }

        // reached end of comment
        $cursor->gotoPosition( $endPosition + 2 );
        $commentBlock = new ezcTemplateDocCommentTstNode( $this->parser->source, clone $this->startCursor, clone $cursor );
        $commentBlock->commentText = substr( $commentBlock->text(), 2, -2 );
        $this->appendElement( $commentBlock );
        return true;
    }
}

?>
