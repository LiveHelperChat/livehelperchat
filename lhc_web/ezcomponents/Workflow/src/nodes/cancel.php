<?php
/**
 * File containing the ezcWorkflowNodeCancel class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node implements the Cancel Case workflow pattern.
 *
 * A complete process instance is removed. This includes currently executing
 * tasks, those which may execute at some future time and all sub-processes.
 * The process instance is recorded as having completed unsuccessfully.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 0..1
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeCancel extends ezcWorkflowNodeEnd
{
    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minOutNodes = 0;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxOutNodes = 1;

    /**
     * Cancels the execution of this workflow.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $activatedFrom
     * @param int                  $threadId
     * @ignore
     */
    public function activate( ezcWorkflowExecution $execution, ezcWorkflowNode $activatedFrom = null, $threadId = 0 )
    {
        $execution->cancel( $this );
    }
}
?>
