<?php
/**
 * File containing the ezcWorkflowNodeSimpleMerge class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Simple Merge (XOR-Join) workflow pattern.
 *
 * The Simple Merge workflow pattern is to be used to merge the possible paths that are defined
 * by a preceding Exclusive Choice. It is assumed that of these possible paths exactly one is
 * taken and no synchronization takes place.
 *
 * Use Case Example: After the payment has been performed by either credit card or bank
 * transfer, the order can be processed further.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * This example displays how you can use a simple merge to tie together two different
 * execution paths from an exclusive choice into one.
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
 *                                                                  new ezcWorkflowConditionGreatherThan( 10 ) ),
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
class ezcWorkflowNodeSimpleMerge extends ezcWorkflowNodeMerge
{
    /**
     * Activate this node.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode $activatedFrom
     * @param int $threadId
     * @ignore
     */
    public function activate( ezcWorkflowExecution $execution, ezcWorkflowNode $activatedFrom = null, $threadId = 0 )
    {
        $parentThreadId = $execution->getParentThreadId( $threadId );

        if ( empty( $this->state['threads'] ) )
        {
            $this->state['threads'][] = $threadId;

            parent::activate( $execution, $activatedFrom, $parentThreadId );
        }
    }

    /**
     * Executes this node.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        return $this->doMerge( $execution );
    }
}
?>
