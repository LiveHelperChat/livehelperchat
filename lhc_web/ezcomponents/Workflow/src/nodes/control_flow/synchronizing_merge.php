<?php
/**
 * File containing the ezcWorkflowNodeSynchronizingMerge class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Synchronizing Merge workflow pattern.
 *
 * The Synchronizing Merge workflow pattern is to be used to synchronize multiple parallel
 * threads of execution that are activated by a preceding Multi-Choice.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * This example displays how you can use ezcWorkflowNodeMultiChoice to activate one or more
 * branches depending on the input and how you can use a synchronizing merge to merge them
 * together again. Execution will not contiue until all activated branches have been completed.
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
 * $input->addOutNode( $choice );
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
class ezcWorkflowNodeSynchronizingMerge extends ezcWorkflowNodeSynchronization
{
}
?>
