<?php
/**
 * File containing the ezcTemplateProgramTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * The root elements for all parser elements.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateProgramTstNode extends ezcTemplateBlockTstNode
{
    /**
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
    }

    public function getTreeProperties()
    {
        return array( 'children' => $this->children );
    }

    /**
     * {@inheritdoc}
     * Returns the column of the starting cursor.
     */
    public function minimumWhitespaceColumn()
    {
        return $this->startCursor->column;
    }

    /**
     * {@inheritdoc}
     * Trims away ending whitespace for all sub-blocks, the trimming of the
     * first text block is not done since this is a program element and not a
     * standard block element.
     */
    public function trimLine( ezcTemplateWhitespaceRemoval $removal )
    {
        if ( count( $this->children ) == 0 )
            return;

        // Tell the removal object to trim text blocks after the current block
        // and after all sub-blocks.
        $removal->trimBlockLines( $this, $this->children );
    }
}
?>
