<?php
/**
 * File containing the ezcTemplateAstNodeGenerator class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Iterates the AST tree and outputs the result as text.
 *
 * Implements the ezcTemplateTstNodeVisitor interface for visiting the nodes
 * and generating the appropriate ast nodes for them.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAstTreeOutput extends ezcTemplateTreeOutput implements ezcTemplateAstNodeVisitor
{
    /**
     * Initialize with correct node class name and regex for extraction.
     * The extraction will remove the prefix <i>ezcTemplate</i> and the suffix
     * <i>AstNode</i>.
     */
    public function __construct()
    {
        parent::__construct( 'ezcTemplateAstNode', "#^ezcTemplate(.+)AstNode#" );
    }

    /**
     * Convenience function for outputting a node.
     * Instantiates the ezcTemplateAstTreeOutput class and calls accept() on
     * $node, the resulting text is returned.
     *
     * @param ezcTemplateAstNode $node
     * @return string
     */
    static public function output( ezcTemplateAstNode $node )
    {
        $treeOutput = new ezcTemplateAstTreeOutput();
        $node->accept( $treeOutput );
        return $treeOutput->text . "\n";
    }

    /**
     * visitLiteralAstNode
     *
     * @param ezcTemplateLiteralAstNode $node
     * @return void
     */
    public function visitLiteralAstNode( ezcTemplateLiteralAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitConstantAstNode
     *
     * @param ezcTemplateConstantAstNode $node
     * @return void
     */
    public function visitConstantAstNode( ezcTemplateConstantAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitEolCommentAstNode
     *
     * @param ezcTemplateEolCommentAstNode $node
     * @return void
     */
    public function visitEolCommentAstNode( ezcTemplateEolCommentAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitBlockCommentAstNode
     *
     * @param ezcTemplateBlockCommentAstNode $node
     * @return void
     */
    public function visitBlockCommentAstNode( ezcTemplateBlockCommentAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitVariableAstNode
     *
     * @param ezcTemplateVariableAstNode $node
     * @return void
     */
    public function visitVariableAstNode( ezcTemplateVariableAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitDynamicVariableAstNode
     *
     * @param ezcTemplateDynamicVariableAstNode $node
     * @return void
     */
    public function visitDynamicVariableAstNode( ezcTemplateDynamicVariableAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitDynamicStringAstNode
     *
     * @param ezcTemplateDynamicStringAstNode $node
     * @return void
     */
    public function visitDynamicStringAstNode( ezcTemplateDynamicStringAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitArrayFetchOperatorAstNode
     *
     * @param ezcTemplateArrayFetchOperatorAstNode $node
     * @return void
     */
    public function visitArrayFetchOperatorAstNode( ezcTemplateArrayFetchOperatorAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitUnaryOperatorAstNode
     *
     * @param ezcTemplateOperatorAstNode $node
     * @return void
     */
    public function visitUnaryOperatorAstNode( ezcTemplateOperatorAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitBinaryOperatorAstNode
     *
     * @param ezcTemplateOperatorAstNode $node
     * @return void
     */
    public function visitBinaryOperatorAstNode( ezcTemplateOperatorAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitFunctionCallAstNode
     *
     * @param ezcTemplateFunctionCallAstNode $node
     * @return void
     */
    public function visitFunctionCallAstNode( ezcTemplateFunctionCallAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitBodyAstNode
     *
     * @param ezcTemplateBodyAstNode $node
     * @return void
     */
    public function visitBodyAstNode( ezcTemplateBodyAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitRootAstNode
     *
     * @param ezcTemplateBodyAstNode $node
     * @return void
     */
    public function visitRootAstNode( ezcTemplateBodyAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitGenericStatementAstNode
     *
     * @param ezcTemplateGenericStatementAstNode $node
     * @return void
     */
    public function visitGenericStatementAstNode( ezcTemplateGenericStatementAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitIfAstNode
     *
     * @param ezcTemplateIfAstNode $node
     * @return void
     */
    public function visitIfAstNode( ezcTemplateIfAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitDynamicBlockAstNode
     *
     * @param ezcTemplateDynamicBlockAstNode $node
     * @return void
     */
    public function visitDynamicBlockAstNode( ezcTemplateDynamicBlockAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }


    /**
     * visitWhileAstNode
     *
     * @param ezcTemplateWhileAstNode $node
     * @return void
     */
    public function visitWhileAstNode( ezcTemplateWhileAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitDoWhileAstNode
     *
     * @param ezcTemplateDoWhileAstNode $node
     * @return void
     */
    public function visitDoWhileAstNode( ezcTemplateDoWhileAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitForAstNode
     *
     * @param ezcTemplateForAstNode $node
     * @return void
     */
    public function visitForAstNode( ezcTemplateForAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitForeachAstNode
     *
     * @param ezcTemplateForeachAstNode $node
     * @return void
     */
    public function visitForeachAstNode( ezcTemplateForeachAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitBreakAstNode
     *
     * @param ezcTemplateBreakAstNode $node
     * @return void
     */
    public function visitBreakAstNode( ezcTemplateBreakAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitContinueAstNode
     *
     * @param ezcTemplateContinueAstNode $node
     * @return void
     */
    public function visitContinueAstNode( ezcTemplateContinueAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitReturnAstNode
     *
     * @param ezcTemplateReturnAstNode $node
     * @return void
     */
    public function visitReturnAstNode( ezcTemplateReturnAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitRequireAstNode
     *
     * @param ezcTemplateRequireAstNode $node
     * @return void
     */
    public function visitRequireAstNode( ezcTemplateRequireAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitRequireOnceAstNode
     *
     * @param ezcTemplateRequireOnceAstNode $node
     * @return void
     */
    public function visitRequireOnceAstNode( ezcTemplateRequireOnceAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitIncludeAstNode
     *
     * @param ezcTemplateIncludeAstNode $node
     * @return void
     */
    public function visitIncludeAstNode( ezcTemplateIncludeAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitIncludeOnceAstNode
     *
     * @param ezcTemplateIncludeOnceAstNode $node
     * @return void
     */
    public function visitIncludeOnceAstNode( ezcTemplateIncludeOnceAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitSwitchAstNode
     *
     * @param ezcTemplateSwitchAstNode $node
     * @return void
     */
    public function visitSwitchAstNode( ezcTemplateSwitchAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitCaseAstNode
     *
     * @param ezcTemplateCaseAstNode $node
     * @return void
     */
    public function visitCaseAstNode( ezcTemplateCaseAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitDefaultAstNode
     *
     * @param ezcTemplateDefaultAstNode $node
     * @return void
     */
    public function visitDefaultAstNode( ezcTemplateDefaultAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitConditionBodyAstNode
     *
     * @param ezcTemplateConditionBodyAstNode $node
     * @return void
     */
    public function visitConditionBodyAstNode( ezcTemplateConditionBodyAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitOutputAstNode
     *
     * @param ezcTemplateOutputAstNode $node
     * @return void
     */
    public function visitOutputAstNode( ezcTemplateOutputAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitTryAstNode
     *
     * @param ezcTemplateTryAstNode $node
     * @return void
     */
    public function visitTryAstNode( ezcTemplateTryAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitCatchAstNode
     *
     * @param ezcTemplateCatchAstNode $node
     * @return void
     */
    public function visitCatchAstNode( ezcTemplateCatchAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitEchoAstNode
     *
     * @param ezcTemplateEchoAstNode $node
     * @return void
     */
    public function visitEchoAstNode( ezcTemplateEchoAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitPrintAstNode
     *
     * @param ezcTemplatePrintAstNode $node
     * @return void
     */
    public function visitPrintAstNode( ezcTemplatePrintAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitIssetAstNode
     *
     * @param ezcTemplateIssetAstNode $node
     * @return void
     */
    public function visitIssetAstNode( ezcTemplateIssetAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitUnsetAstNode
     *
     * @param ezcTemplateUnsetAstNode $node
     * @return void
     */
    public function visitUnsetAstNode( ezcTemplateUnsetAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitEmptyAstNode
     *
     * @param ezcTemplateEmptyAstNode $node
     * @return void
     */
    public function visitEmptyAstNode( ezcTemplateEmptyAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitParenthesisAstNode
     *
     * @param ezcTemplateParenthesisAstNode $node
     * @return void
     */
    public function visitParenthesisAstNode( ezcTemplateParenthesisAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitCurlyBracesAstNode
     *
     * @param ezcTemplateCurlyBracesAstNode $node
     * @return void
     */
    public function visitCurlyBracesAstNode( ezcTemplateCurlyBracesAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitTypeCastAstNode
     *
     * @param ezcTemplateTypeCastAstNode $node
     * @return void
     */
    public function visitTypeCastAstNode( ezcTemplateTypeCastAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * visitNopAstNode
     *
     * @param ezcTemplateNopAstNode $node
     * @return void
     */
    public function visitNopAstNode( ezcTemplateNopAstNode $node )
    {
        $this->text .= $this->outputNode( $node );
    }

    /**
     * Extracts position data from the specified node and set in the out parameters.
     * Ast nodes has no position so it always returns false.
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
        return false;
    }

    /**
     * Extracts the properties from the specified node and returns it as an array.
     * The properties are taken using get_object_vars().
     *
     * @param Object $node The node to examine.
     * @return array(name=>value)
     */
    protected function extractNodeProperties( $node )
    {
        return get_object_vars( $node );
    }
}
?>
