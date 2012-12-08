<?php
/**
 * File containing the ezcTemplateModifyingBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Block element containing an modifying expression.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateModifyingBlockTstNode extends ezcTemplateBlockTstNode
{
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
     * The root of the parsed modifying expression.
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
        $this->startBracket = '{';
        $this->endBracket = '}';
        $this->expressionRoot = null;

        $this->isNestingBlock = false;
   }

    public function getTreeProperties()
    {
        return array( 'startBracket'   => $this->startBracket,
                      'endBracket'     => $this->endBracket,
                      'expressionRoot' => $this->expressionRoot );
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
