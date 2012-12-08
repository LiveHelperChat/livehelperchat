<?php
/**
 * File containing the ezcTemplateBlockCommentTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Inline block comments in parser trees.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBlockCommentTstNode extends ezcTemplateExpressionTstNode
{
    /**
     * The parsed comment text without the start and end markers.
     *
     * @var string
     */
    public $commentText;

    /**
     * Constructs a new block comment.
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->commentText = null;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'commentText' => $this->commentText );
    }
}
?>
