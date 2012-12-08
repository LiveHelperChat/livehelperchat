<?php
/**
 * File containing the ezcTemplateForeachLoopTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Control structure: foreach.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateForeachLoopTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The array that should be iterated over.
     *
     * @var ezcTemplateExpressionTstNode
     */
    public $array;

    /**
     * The key variable.
     *
     * @var ezcTemplateVariableTstNode
     */
    public $keyVariableName;

    /**
     * The item variable.
     *
     * @var ezcTemplateVariableTstNode
     */
    public $itemVariableName;

    /**
     * Unknown.
     * TODO: It is used, but why?
     *
     * @var mixed
     */
    public $value;

    /**
     * The increment statement.
     *
     * @var ezcTemplateVariableTstNode
     */
    public $increment;

    /**
     * The decrement statement.
     *
     * @var ezcTemplateVariableTstNode
     */
    public $decrement;

    /**
     * The offset
     *
     * @var ezcTemplateExpressionTstNode
     */
    public $offset;

    /**
     * The limit
     *
     * @var ezcTemplateExpressionTstNode
     */
    public $limit;

    /**
     * Constructs a new ezcTemplateForeachLoopTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->value = $this->keyVariableName = $this->itemVariableName = null;
        $this->name = 'foreach';

        $this->increment = array();
        $this->decrement = array();

        $this->offset = $this->limit = null;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'name'             => $this->name,
                      'isClosingBlock'   => $this->isClosingBlock,
                      'isNestingBlock'   => $this->isNestingBlock,
                      'array'            => $this->array,
                      'keyVariableName'  => $this->keyVariableName,
                      'itemVariableName' => $this->itemVariableName,
                      'increment'        => $this->increment,
                      'decrement'        => $this->decrement,
                      'value'            => $this->value,
                      'children'         => $this->children );
    }

    /**
     * Returns true if the given element can be handled.
     *
     * @param ezcTEmplateTstNode $element
     * @return bool
     */
    public function canHandleElement( ezcTemplateTstNode $element )
    {
        // return ( $element instanceof ezcTemplateLoopTstNode && $element->name != 'delimiter' );
        return false;
    }

    /**
     * Handle the element.
     *
     * @param ezcTemplateTstNode $element
     * @return void
     */
    public function handleElement( ezcTemplateTstNode $element )
    {
        // Also accept the Delimiter TSTNode.
        $this->children[] = $element;
    }
}
?>
