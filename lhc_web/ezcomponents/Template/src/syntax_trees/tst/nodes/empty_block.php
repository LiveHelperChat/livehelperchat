<?php
/**
 * File containing the ezcTemplateEmptyBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Block element containing an empty block.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateEmptyBlockTstNode extends ezcTemplateBlockTstNode
{
    /**
     * Constructs a new ezcTemplateEmptyBlockTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->isNestingBlock = false;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array();
    }

    /**
     * Returns true since empty block elements can always be children of blocks.
     *
     * @return true
     */
     /*
    public function canBeChildOf( ezcTemplateBlockTstNode $block )
    {
        // Empty block elements can always be child of blocks
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
