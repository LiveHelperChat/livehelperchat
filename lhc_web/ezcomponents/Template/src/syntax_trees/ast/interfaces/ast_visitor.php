<?php
/**
 * File containing the ezcTemplateBasicAstNodeVisitor class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Visitor interface for the basic nodes.
 *
 * This interface defines the methods for all the generic nodes.
 *
 * The acyclic visitor pattern is used as the basis of this interface. Combining
 * this interface with other specialized ones allows the implementation of classes
 * which can visit all kinds of nodes.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
interface ezcTemplateAstNodeVisitor// extends ezcTemplateAstNodeVisitor
{
    /**
     * Visits a node containing a builtin constant type.
     *
     * @param ezcTemplateLiteralAstNode $node The node containing the constant value.
     * @return void
     */
    public function visitLiteralAstNode( ezcTemplateLiteralAstNode $node );

    /**
     * Visits a node containing an constant.
     *
     * @param ezcTemplateConstantAstNode $node The node containing the constant value.
     * @return void
     */
    public function visitConstantAstNode( ezcTemplateConstantAstNode $node );

    /**
     * Visits a node containing an variable.
     *
     * @param ezcTemplateVariableAstNode $node The node containing the variable value.
     * @return void
     */
    public function visitVariableAstNode( ezcTemplateVariableAstNode $node );

    /**
     * Visits a node containing a dynamic variable.
     *
     * @param ezcTemplateDynamicVariableAstNode $node The node containing the dynamic variable value.
     * @return void
     */
    public function visitDynamicVariableAstNode( ezcTemplateDynamicVariableAstNode $node );

    /**
     * Visits a node containing a dynamic string.
     *
     * @param ezcTemplateDynamicStringAstNode $node The node containing the dynamic string.
     * @return void
     */
    public function visitDynamicStringAstNode( ezcTemplateDynamicStringAstNode $node );

    /**
     * Visits a node containing an array fetch operator type.
     *
     * @param ezcTemplateArrayFetchOperatorAstNode $node The node containing the array fetch operator.
     * @return void
     */
    public function visitArrayFetchOperatorAstNode( ezcTemplateArrayFetchOperatorAstNode $node );

    /**
     * Visits a node containing a unary operator type.
     * Unary operators take one parameter and consist of a symbol.
     *
     * @param ezcTemplateOperatorAstNode $node The node containing the operator with parameter.
     * @return void
     */
    public function visitUnaryOperatorAstNode( ezcTemplateOperatorAstNode $node );

    /**
     * Visits a node containing a binary operator type.
     * Binary operators take two parameters and consist of a symbol.
     *
     * @param ezcTemplateOperatorAstNode $node The node containing the operator with parameters.
     * @return void
     */
    public function visitBinaryOperatorAstNode( ezcTemplateOperatorAstNode $node );

    /**
     * Visits a node containing a function call.
     * Function call consist of a function name and arguments.
     *
     * @param ezcTemplateFunctionCallAstNode $node The node containing the function call with arguments.
     * @return void
     */
    public function visitFunctionCallAstNode( ezcTemplateFunctionCallAstNode $node );

    /**
     * Visits a node containing a body of statements.
     * A body consists of a series of statements in sequence.
     *
     * @param ezcTemplateBodyAstNode $node The node containing the body.
     * @return void
     */
    public function visitBodyAstNode( ezcTemplateBodyAstNode $node );

    /**
     * Visits the node $node containing output.
     *
     * @param ezcTemplateOutputAstNode $node
     * @return void
     */
    public function visitOutputAstNode( ezcTemplateOutputAstNode $node );

    /**
     * Visits a node containing a generic statement.
     * A generic statement contains a generic code expression but is terminated with a semi-colon.
     *
     * @param ezcTemplateGenericStatementAstNode $node The node containing the generic statement.
     * @return void
     */
    public function visitGenericStatementAstNode( ezcTemplateGenericStatementAstNode $node );

    /**
     * Visits a node containing if control structures.
     *
     * @param ezcTemplateIfAstNode $node The node containing the if control structure.
     * @return void
     */
    public function visitIfAstNode( ezcTemplateIfAstNode $node );

    /**
     * Visits a node containing while control structures.
     *
     * @param ezcTemplateWhileAstNode $node The node containing the while control structure.
     */
    public function visitWhileAstNode( ezcTemplateWhileAstNode $node );

    /**
     * Visits a node containing conditions for if control structures.
     *
     * @param ezcTemplateConditionBodyAstNode $node The node containing the if condition.
     * @return void
     */
    public function visitConditionBodyAstNode( ezcTemplateConditionBodyAstNode $node );

    /**
     * Visits a node containing foreach control structures.
     *
     * @param ezcTemplateForeachAstNode $node The node containing the foreach control structure.
     * @return void
     */
    public function visitForeachAstNode( ezcTemplateForeachAstNode $node );

    /**
     * Visits a node containing break control structures.
     *
     * @param ezcTemplateBreakAstNode $node The node containing the break control structure.
     * @return void
     */
    public function visitBreakAstNode( ezcTemplateBreakAstNode $node );

    /**
     * Visits a node containing continue control structures.
     *
     * @param ezcTemplateContinueAstNode $node The node containing the continue control structure.
     * @return void
     */
    public function visitContinueAstNode( ezcTemplateContinueAstNode $node );

    /**
     * Visits a node containing switch control structures.
     *
     * @param ezcTemplateSwitchAstNode $node The node containing the switch control structure.
     * @return void
     */
    public function visitSwitchAstNode( ezcTemplateSwitchAstNode $node );

    /**
     * Visits a node containing case control structures.
     *
     * @param ezcTemplateCaseAstNode $node The node containing the case control structure.
     * @return void
     */
    public function visitCaseAstNode( ezcTemplateCaseAstNode $node );

    /**
     * Visits a node containing default case control structures.
     *
     * @param ezcTemplateDefaultAstNode $node The node containing the default case control structure.
     * @return void
     */
    public function visitDefaultAstNode( ezcTemplateDefaultAstNode $node );

    /**
     * Visits a node containing return control structures.
     *
     * @param ezcTemplateReturnAstNode $node The node containing the return control structure.
     * @return void
     */
    public function visitReturnAstNode( ezcTemplateReturnAstNode $node );

    /**
     * Visits a node containing require control structures.
     *
     * @param ezcTemplateRequireAstNode $node The node containing the require control structure.
     * @return void
     */
    public function visitRequireAstNode( ezcTemplateRequireAstNode $node );

    /**
     * Visits a node containing require_once control structures.
     *
     * @param ezcTemplateRequireOnceAstNode $node The node containing the require_once control structure.
     * @return void
     */
    public function visitRequireOnceAstNode( ezcTemplateRequireOnceAstNode $node );

    /**
     * Visits a node containing include control structures.
     *
     * @param ezcTemplateIncludeAstNode $node The node containing the include control structure.
     * @return void
     */
    public function visitIncludeAstNode( ezcTemplateIncludeAstNode $node );

    /**
     * Visits a node containing include_once control structures.
     *
     * @param ezcTemplateIncludeOnceAstNode $node The node containing the include_once control structure.
     * @return void
     */
    public function visitIncludeOnceAstNode( ezcTemplateIncludeOnceAstNode $node );

    /**
     * Visits a node containing try control structures.
     *
     * @param ezcTemplateTryAstNode $node The node containing the try control structure.
     * @return void
     */
    public function visitTryAstNode( ezcTemplateTryAstNode $node );

    /**
     * Visits a node containing catch control structures.
     *
     * @param ezcTemplateCatchAstNode $node The node containing the catch control structure.
     * @return void
     */
    public function visitCatchAstNode( ezcTemplateCatchAstNode $node );

    /**
     * Visits a node containing echo construct.
     *
     * @param ezcTemplateEchoAstNode $node The node containing the echo construct.
     * @return void
     */
    public function visitEchoAstNode( ezcTemplateEchoAstNode $node );

    /**
     * Visits a node containing print construct.
     *
     * @param ezcTemplatePrintAstNode $node The node containing the print construct.
     * @return void
     */
    public function visitPrintAstNode( ezcTemplatePrintAstNode $node );

    /**
     * Visits a node containing isset construct.
     *
     * @param ezcTemplateIssetAstNode $node The node containing the isset construct.
     * @return void
     */
    public function visitIssetAstNode( ezcTemplateIssetAstNode $node );

    /**
     * Visits a node containing unset construct.
     *
     * @param ezcTemplateUnsetAstNode $node The node containing the unset construct.
     * @return void
     */
    public function visitUnsetAstNode( ezcTemplateUnsetAstNode $node );

    /**
     * Visits a node containing empty construct.
     *
     * @param ezcTemplateEmptyAstNode $node The node containing the empty construct.
     * @return void
     */
    public function visitEmptyAstNode( ezcTemplateEmptyAstNode $node );

    /**
     * Visits a node containing type cast construct.
     *
     * @param ezcTemplateTypeCastAstNode $node The node containing the type cast construct.
     * @return void
     */
    public function visitTypeCastAstNode( ezcTemplateTypeCastAstNode $node );

    /**
     * Visits a node containing a nop node.
     *
     * @param ezcTemplateNopAstNode $node The node containing the nop node.
     * @return void
     */
    public function visitNopAstNode( ezcTemplateNopAstNode $node );
}
?>
