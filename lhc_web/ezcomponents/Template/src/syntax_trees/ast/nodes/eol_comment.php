<?php
/**
 * File containing the ezcTemplateEolCommentAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents PHP comments (EOL style).
 *
 * Comments consists of the start marker and then the comment text. If the text
 * contains newlines it will be split into multiple comment lines all starting
 * with the same marker.
 * The start marker is either a
 * {@link ezcTemplateEolCommentAstNode::MARKER_DOUBLE_SLASH double slash} or a
 * {@link ezcTemplateEolCommentAstNode::MARKER_HASH hash}.
 *
 * Creating a comment:
 * <code>
 * $var = new ezcTemplateEolCommentAstNode( 'A comment with some text' );
 * </code>
 * The corresponding PHP code will be:
 * <code>
 * // A comment with some text
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateEolCommentAstNode extends ezcTemplateStatementAstNode
{
    /**
     * Comment start marker is a double slash.
     */
    const MARKER_DOUBLE_SLASH = 1;

    /**
     * Comment start marker is a hash.
     */
    const MARKER_HASH         = 2;

    /**
     * The text for the comment.
     *
     * @var string
     */
    public $text;

    /**
     * Controls whether space separators are placed between the marker and the
     * comment text.
     *
     * @var bool
     */
    public $hasSeparator;

    /**
     * Type of EOL comment.
     * The type can be on eof:
     * - {@link self::MARKER_DOUBLE_SLASH} for // 
     * - {@link self::MARKER_HASH} for #
     *
     * @var int
     */
    public $type;

    /**
     * Constructs a new ezcTemplateEolCommentAstNode
     *
     * @param string $text         Text for comment.
     * @param bool   $hasSeparator Use spacing separator or not?
     * @param int    $type         Type of EOL comment, see {@link self::$type}.
     */
    public function __construct( $text, $hasSeparator = true, $type = ezcTemplateEolCommentAstNode::MARKER_DOUBLE_SLASH )
    {
        parent::__construct();
        if ( !is_string( $text ) )
        {
            throw new ezcBaseValueException( "text", $text, 'string' );
        }
        $this->text         = $text;
        $this->type         = $type;
        $this->hasSeparator = $hasSeparator;
    }

    /**
     * Returns the text representation of the marker {@link self::$type type}.
     *
     * @return string
     */
    public function createMarkerText()
    {
        switch ( $this->type )
        {
            case self::MARKER_DOUBLE_SLASH:
                return '//';
            case self::MARKER_HASH:
                return '#';
        }
    }
}
?>
