<?php
/**
 * File containing the ezcWorkflowNodeExclusiveChoice class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Exclusive Choice workflow pattern.
 *
 * The Exclusive Choice workflow pattern defines multiple possible paths
 * for the workflow of which exactly one is chosen based on the conditions
 * set for the out nodes.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 2..*
 *
 * This example displays how you can use an exclusive choice to select one of two
 * possible branches depending on the workflow variable 'value' which is read using
 * an input node.
 *
 * <code>
 * <?php
 * $workflow = new ezcWorkflow( 'Test' );
 *
 * // wait for input into the workflow variable value.
 * $input = new ezcWorkflowNodeInput( array( 'value' => new ezcWorkflowConditionIsInt ) );
 * $workflow->startNode->addOutNode( $input );
 *
 * // create the exclusive choice branching node
 * $choice = new ezcWorkflowNodeExclusiveChoice;
 * $intput->addOutNode( $choice );
 *
 * $branch1 = ....; // create nodes for the first branch of execution here..
 * $branch2 = ....; // create nodes for the second branch of execution here..
 *
 * // add the outnodes and set the conditions on the exclusive choice
 * $choice->addConditionalOutNode( new ezcWorkflowConditionVariable( 'value',
 *                                                                  new ezcWorkflowConditionGreaterThan( 10 ) ),
 *                                $branch1 );
 * $choice->addConditionalOutNode( new ezcWorkflowConditionVariable( 'value',
 *                                                                  new ezcWorkflowConditionLessThan( 11 ) ),
 *                                $branch2 );
 *
 * // Merge the two branches together and continue execution.
 * $merge = new ezcWorkflowNodeSimpleMerge();
 * $merge->addInNode( $branch1 );
 * $merge->addInNode( $branch2 );
 * $merge->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeExclusiveChoice extends ezcWorkflowNodeConditionalBranch
{
    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to have. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minConditionalOutNodes = 2;

    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minActivatedConditionalOutNodes = 1;

    /**
     * Constraint: The maximum number of conditional outgoing nodes this node
     * may activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxActivatedConditionalOutNodes = 1;
}
?>
