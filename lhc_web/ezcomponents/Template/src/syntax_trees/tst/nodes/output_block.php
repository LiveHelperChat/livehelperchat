<?php
/**
 * File containing the ezcTemplateOutputBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Block element containing an output expression.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateOutputBlockTstNode extends ezcTemplateBlockTstNode
{
    /** 
     *  Should this node processed raw? 
     *  The ContextAppender will not append a context for this node.
     */
    public $isRaw;

    /**
     * The bracket start character.
     * @var string
     */
    public $startBracket;

    /**
     * The bracket end character.
     * @var string
     */
    public $endBracket;

    /**
     * The node starting the output expression.
     *
     * @var ezcTemplateExpressionTstNode
     */
//    public $element; // removed, not needed

    /**
     * The root of the parsed output expression.
     */
    public $expressionRoot;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
//        $this->element = null; // removed, not needed
        $this->startBracket = '{';
        $this->endBracket = '}';
        $this->expressionRoot = null;

        $this->isNestingBlock = false;
    }

    public function getTreeProperties()
    {
        return array( 'startBracket'   => $this->startBracket,
                      'endBracket'     => $this->endBracket,
                      'expressionRoot' => $this->expressionRoot,
                      'isRaw'          => $this->isRaw );
    }

    /**
     * Returns true since output expression block elements can always be children of blocks.
     *
     * @return true
     */
     /*
    public function canBeChildOf( ezcTemplateBlockTstNode $block )
    {
        // Output expression block elements can always be child of blocks
        return true;
    }
    */

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
