<?php
/**
 * File containing the ezcWorkflowExecutionPlugin class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for workflow execution engine plugins.
 *
 * @package Workflow
 * @version 1.4.1
 */
abstract class ezcWorkflowExecutionPlugin
{
    /**
     * Called after an execution has been started.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionStarted( ezcWorkflowExecution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been suspended.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionSuspended( ezcWorkflowExecution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been resumed.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionResumed( ezcWorkflowExecution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has been cancelled.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionCancelled( ezcWorkflowExecution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after an execution has successfully ended.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionEnded( ezcWorkflowExecution $execution )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a node is activated.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     * @return bool true, when the node should be activated, false otherwise
     */
    public function beforeNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
    // @codeCoverageIgnoreStart
        return true;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a node has been activated.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a node has been executed.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeExecuted( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a new thread has been started.
     *
     * @param ezcWorkflowExecution $execution
     * @param int                  $threadId
     * @param int                  $parentId
     * @param int                  $numSiblings
     */
    public function afterThreadStarted( ezcWorkflowExecution $execution, $threadId, $parentId, $numSiblings )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a thread has ended.
     *
     * @param ezcWorkflowExecution $execution
     * @param int                  $threadId
     */
    public function afterThreadEnded( ezcWorkflowExecution $execution, $threadId )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a variable is set.
     *
     * @param  ezcWorkflowExecution $execution
     * @param  string               $variableName
     * @param  mixed                $value
     * @return mixed the value the variable should be set to
     */
    public function beforeVariableSet( ezcWorkflowExecution $execution, $variableName, $value )
    {
    // @codeCoverageIgnoreStart
        return $value;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a variable has been set.
     *
     * @param ezcWorkflowExecution $execution
     * @param string               $variableName
     * @param mixed                $value
     */
    public function afterVariableSet( ezcWorkflowExecution $execution, $variableName, $value )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called before a variable is unset.
     *
     * @param  ezcWorkflowExecution $execution
     * @param  string               $variableName
     * @return bool true, when the variable should be unset, false otherwise
     */
    public function beforeVariableUnset( ezcWorkflowExecution $execution, $variableName )
    {
    // @codeCoverageIgnoreStart
        return true;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Called after a variable has been unset.
     *
     * @param ezcWorkflowExecution $execution
     * @param string               $variableName
     */
    public function afterVariableUnset( ezcWorkflowExecution $execution, $variableName )
    {
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd
}
?>
