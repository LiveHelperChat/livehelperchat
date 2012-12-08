<?php
/**
 * File containing the ezcWorkflowNodeMultiChoice class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Multi-Choice workflow pattern.
 *
 * The Multi-Choice workflow pattern defines multiple possible paths for the workflow of
 * which one or more are chosen. It is a generalization of the Parallel Split and
 * Exclusive Choice workflow patterns.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 2..*
 *
 * This example displays how you can use ezcWorkflowNodeMultiChoice to activate one or more
 * branches depending on the input.  Note that an input value of 5 will start only branch 1
 * while an input value of 11 or more will start both branch1 and branch2.
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
 * $choice = new ezcWorkflowNodeMultiChoice;
 * $intput->addOutNode( $choice );
 *
 * $branch1 = ....; // create nodes for the first branch of execution here..
 * $branch2 = ....; // create nodes for the second branch of execution here..
 *
 * // add the outnodes and set the conditions on the exclusive choice
 * $choice->addConditionalOutNode( new ezcWorkflowConditionVariable( 'value',
 *                                                                  new ezcWorkflowConditionGreaterThan( 1 ) ),
 *                                $branch1 );
 * $choice->addConditionalOutNode( new ezcWorkflowConditionVariable( 'value',
 *                                                                  new ezcWorkflowConditionGreaterThan( 10 ) ),
 *                                $branch2 );
 *
 * // Merge the two branches together and continue execution.
 * $merge = new ezcWorkflowNodeSynchronizingMerge();
 * $merge->addInNode( $branch1 );
 * $merge->addInNode( $branch2 );
 * $merge->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeMultiChoice extends ezcWorkflowNodeConditionalBranch
{
}
?>
