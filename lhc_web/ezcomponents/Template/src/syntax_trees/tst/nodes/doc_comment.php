<?php
/**
 * File containing the ezcTemplateDocCommentTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Doc comment element in parse trees.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDocCommentTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The parsed comment text without the start and end markers.
     *
     * @var string
     */
    public $commentText;

    /**
     * Constructs a new ezcTemplateDocCommentTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->commentText = null;
        $this->isNestingBlock = false;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'name'        => $this->name,
                      'commentText' => $this->commentText );
    }

    /**
     * Returns true since doc comment elements can always be children of blocks.
     *
     * @return true
     */
     /*
    public function canBeChildOf( ezcTemplateBlockTstNode $block )
    {
        // Doc comment elements can always be child of blocks
        return true;
    }
    */

    /**
     * Returns the column of the starting cursor.
     *
     * @return int
     */
    public function minimumWhitespaceColumn()
    {
        return $this->startCursor->column;
    }
}
?>
