<?php
/**
 * File containing the ezcTemplateTstTreeOutput class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Iterates the TST tree and outputs the result as text.
 *
 * Implements the ezcTemplateTstNodeVisitor interface for visiting the nodes
 * and generating the appropriate tst nodes for them.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateTstTreeOutput extends ezcTemplateTreeOutput implements ezcTemplateTstNodeVisitor
{
    /**
     * Initialize with correct node class name and regex for extraction.
     * The extraction will remove the prefix <i>ezcTemplate</i> and the suffix
     * <i>TstNode</i>.
     */
    public function __construct()
    {
        parent::__construct( 'ezcTemplateTstNode', "#^ezcTemplate(.+)TstNode#" );
    }

    /**
     * Convenience function for outputting a node.
     * Instantiates the ezcTemplateTstTreeOutput class and calls accept() on
     * $node, the resulting text is returned.
     *
     * @param ezcTemplateAstNode $node
     * @return string
     */
    static public function output( ezcTemplateTstNode $node )
    {
        $treeOutput = new ezcTemplateTstTreeOutput();
        $node->accept( $treeOutput );
        return $treeOutput->text . "\n";
    }

    /**
     * @return void
     */
    public function visitProgramTstNode( ezcTemplateProgramTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitCustomBlockTstNode( ezcTemplateCustomBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLiteralBlockTstNode( ezcTemplateLiteralBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitEmptyBlockTstNode( ezcTemplateEmptyBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitParenthesisTstNode( ezcTemplateParenthesisTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitOutputBlockTstNode( ezcTemplateOutputBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitModifyingBlockTstNode( ezcTemplateModifyingBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLiteralTstNode( ezcTemplateLiteralTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitVariableTstNode( ezcTemplateVariableTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitTextBlockTstNode( ezcTemplateTextBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitFunctionCallTstNode( ezcTemplateFunctionCallTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitDocCommentTstNode( ezcTemplateDocCommentTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitForeachLoopTstNode( ezcTemplateForeachLoopTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitDelimiterTstNode( ezcTemplateDelimiterTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitWhileLoopTstNode( ezcTemplateWhileLoopTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitIfConditionTstNode( ezcTemplateIfConditionTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitConditionBodyTstNode( ezcTemplateConditionBodyTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLoopTstNode( ezcTemplateLoopTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPropertyFetchOperatorTstNode( ezcTemplatePropertyFetchOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitArrayFetchOperatorTstNode( ezcTemplateArrayFetchOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPlusOperatorTstNode( ezcTemplatePlusOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitMinusOperatorTstNode( ezcTemplateMinusOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitConcatOperatorTstNode( ezcTemplateConcatOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitMultiplicationOperatorTstNode( ezcTemplateMultiplicationOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitDivisionOperatorTstNode( ezcTemplateDivisionOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitModuloOperatorTstNode( ezcTemplateModuloOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitEqualOperatorTstNode( ezcTemplateEqualOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitNotEqualOperatorTstNode( ezcTemplateNotEqualOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitIdenticalOperatorTstNode( ezcTemplateIdenticalOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitNotIdenticalOperatorTstNode( ezcTemplateNotIdenticalOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLessThanOperatorTstNode( ezcTemplateLessThanOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitGreaterThanOperatorTstNode( ezcTemplateGreaterThanOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLessEqualOperatorTstNode( ezcTemplateLessEqualOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitGreaterEqualOperatorTstNode( ezcTemplateGreaterEqualOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLogicalAndOperatorTstNode( ezcTemplateLogicalAndOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLogicalOrOperatorTstNode( ezcTemplateLogicalOrOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitAssignmentOperatorTstNode( ezcTemplateAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPlusAssignmentOperatorTstNode( ezcTemplatePlusAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitMinusAssignmentOperatorTstNode( ezcTemplateMinusAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitMultiplicationAssignmentOperatorTstNode( ezcTemplateMultiplicationAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitDivisionAssignmentOperatorTstNode( ezcTemplateDivisionAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitConcatAssignmentOperatorTstNode( ezcTemplateConcatAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitModuloAssignmentOperatorTstNode( ezcTemplateModuloAssignmentOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPreIncrementOperatorTstNode( ezcTemplatePreIncrementOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPreDecrementOperatorTstNode( ezcTemplatePreDecrementOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPostIncrementOperatorTstNode( ezcTemplatePostIncrementOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitPostDecrementOperatorTstNode( ezcTemplatePostDecrementOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitNegateOperatorTstNode( ezcTemplateNegateOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitLogicalNegateOperatorTstNode( ezcTemplateLogicalNegateOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitBlockCommentTstNode( ezcTemplateBlockCommentTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitEolCommentTstNode( ezcTemplateEolCommentTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitBlockTstNode( ezcTemplateBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitDynamicBlockTstNode( ezcTemplateDynamicBlockTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitCacheTstNode( ezcTemplateCacheTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitDeclarationTstNode( ezcTemplateDeclarationTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitCycleControlTstNode( ezcTemplateCycleControlTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitIncludeTstNode( ezcTemplateIncludeTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitReturnTstNode( ezcTemplateReturnTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }
 
    /**
     * @return void
     */
    public function visitSwitchTstNode( ezcTemplateSwitchTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }
 
    /**
     * @return void
     */
    public function visitCaseTstNode( ezcTemplateCaseTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }
 
    /**
     * @return void
     */
    public function visitLiteralArrayTstNode( ezcTemplateLiteralArrayTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * @return void
     */
    public function visitArrayRangeOperatorTstNode( ezcTemplateArrayRangeOperatorTstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }
  
    /**
     * Extracts position data from the specified node and set in the out parameters.
     * The position is taken from ezcTemplateTstNode::startCursor and ezcTemplateTstNode::endCursor.
     *
     * @param Object $node The node to examine.
     * @param int    $startLine   The starting line for the node.
     * @param int    $startColumn The starting column for the node.
     * @param int    $endLine     The starting line for the node.
     * @param int    $endColumn   The starting column for the node.
     * @return bool True if the extraction was succesful.
     */
    protected function extractNodePosition( $node, &$startLine, &$startColumn, &$endLine, &$endColumn )
    {
        $startLine   = $node->startCursor->line;
        $startColumn = $node->startCursor->column;
        $endLine     = $node->endCursor->line;
        $endColumn   = $node->endCursor->column;
        return true;
    }

    /**
     * Extracts the properties from the specified node and returns it as an array.
     * The properties are taken from ezcTemplateTstNode::treeProperties.
     *
     * @param Object $node The node to examine.
     * @return array(name=>value)
     */
    protected function extractNodeProperties( $node )
    {
        return $node->treeProperties;
    }
}
?>
