<?php
/**
 * File containing the ezcWorkflowNodeSynchronization class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Synchronization (AND-Join) workflow pattern.
 *
 * The Synchronization workflow pattern synchronizes multiple parallel threads of execution
 * into a single thread of execution.
 *
 * Workflow execution continues once all threads of execution that are to be synchronized have
 * finished executing (exactly once).
 *
 * Use Case Example: After the confirmation email has been sent and the shipping process has
 * been completed, the order can be archived.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 1
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeSynchronization extends ezcWorkflowNodeMerge
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
        $this->prepareActivate( $execution, $threadId );
        parent::activate( $execution, $activatedFrom, $execution->getParentThreadId( $threadId ) );
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
        if ( count( $this->state['threads'] ) == $this->state['siblings'] )
        {
            return $this->doMerge( $execution );
        }
        else
        {
            return false;
        }
    }
}
?>
