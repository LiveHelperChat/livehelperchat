<?php
/**
 * File containing the ezcTemplateExpressionTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Interface for expression nodes in parser trees.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateExpressionTstNode extends ezcTemplateTstNode
{
    /**
     * Constructs a new ezcTemplateExpressionTstNode
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
    }

    /**
     * Returns false since expression nodes can never be children of blocks.
     *
     * @param ezcTemplateBlockTstNode $block
     * @return true
     */
    public function canBeChildOf( ezcTemplateBlockTstNode $block )
    {
        // Expression nodes can never be children of blocks,
        // these nodes should only be used inside expressions
        return false;
    }

    /**
     * {@inheritdoc}
     * Returns the column of the starting cursor.
     */
    public function minimumWhitespaceColumn()
    {
        return $this->startCursor->column;
    }

}
?>
