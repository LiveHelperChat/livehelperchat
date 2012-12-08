<?php
/**
 * File containing the ezcWorkflowNodeParallelSplit class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Parallel Split workflow pattern.
 *
 * The Parallel Split workflow pattern divides one thread of execution
 * unconditionally into multiple parallel threads of execution.
 *
 * Use Case Example: After the credit card specified by the customer has been successfully
 * charged, the activities of sending a confirmation email and starting the shipping process can
 * be executed in parallel.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 2..*
 *
 * This example creates a workflow that splits in two parallel threads which
 * are joined again using a ezcWorkflowNodeDiscriminator.
 *
 * <code>
 * <?php
 * $workflow = new ezcWorkflow( 'Test' );
 *
 * $split = new ezcWorkflowNodeParallelSplit();
 * $workflow->startNode->addOutNode( $split );
 * $nodeExec1 = ....; // create nodes for the first thread of execution here..
 * $nodeExec2 = ....; // create nodes for the second thread of execution here..
 *
 * $disc = new ezcWorkflowNodeDiscriminator();
 * $disc->addInNode( $nodeExec1 );
 * $disc->addInNode( $nodeExec2 );
 * $disc->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeParallelSplit extends ezcWorkflowNodeBranch
{
    /**
     * Activates all outgoing nodes.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        return $this->activateOutgoingNodes( $execution, $this->outNodes );
    }
}
?>
