<?php
/**
 * File containing the ezcWorkflowExecutionListenerPlugin class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Execution plugin that notifies ezcWorkflowExecutionListener objects.
 *
 * @package Workflow
 * @version 1.4.1
 * @access private
 */
class ezcWorkflowExecutionListenerPlugin extends ezcWorkflowExecutionPlugin
{
    /**
     * Listeners.
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Adds a listener.
     *
     * @param ezcWorkflowExecutionListener $listener
     * @return bool true when the listener was added, false otherwise.
     */
    public function addListener( ezcWorkflowExecutionListener $listener )
    {
        if ( ezcWorkflowUtil::findObject( $this->listeners, $listener ) !== false )
        {
            return false;
        }

        $this->listeners[] = $listener;

        return true;
    }

    /**
     * Removes a listener.
     *
     * @param ezcWorkflowExecutionListener $listener
     * @return bool true when the listener was removed, false otherwise.
     */
    public function removeListener( ezcWorkflowExecutionListener $listener )
    {
        $index = ezcWorkflowUtil::findObject( $this->listeners, $listener );

        if ( $index === false )
        {
            return false;
        }

        unset( $this->listeners[$index] );

        return true;
    }

    /**
     * Notify listeners.
     *
     * @param string $message
     * @param int    $type
     */
    protected function notifyListeners( $message, $type = ezcWorkflowExecutionListener::INFO )
    {
        foreach ( $this->listeners as $listener )
        {
            $listener->notify( $message, $type );
        }
    }

    /**
     * Called after an execution has been started.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionStarted( ezcWorkflowExecution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Started execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          )
        );
    }

    /**
     * Called after an execution has been suspended.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionSuspended( ezcWorkflowExecution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Suspended execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          )
        );
    }

    /**
     * Called after an execution has been resumed.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionResumed( ezcWorkflowExecution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Resumed execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          )
        );
    }

    /**
     * Called after an execution has been cancelled.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionCancelled( ezcWorkflowExecution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Cancelled execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          )
        );
    }

    /**
     * Called after an execution has successfully ended.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionEnded( ezcWorkflowExecution $execution )
    {
        $this->notifyListeners(
          sprintf(
            'Ended execution #%d of workflow "%s" (version %d).',

            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          )
        );
    }

    /**
     * Called after a node has been activated.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $this->notifyListeners(
          sprintf(
            'Activated node #%d(%s) for instance #%d of workflow "%s" (version %d).',

            $node->getId(),
            get_class( $node ),
            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          ),
          ezcWorkflowExecutionListener::DEBUG
        );
    }

    /**
     * Called after a node has been executed.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeExecuted( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $this->notifyListeners(
          sprintf(
            'Executed node #%d(%s) for instance #%d of workflow "%s" (version %d).',

            $node->getId(),
            get_class( $node ),
            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          ),
          ezcWorkflowExecutionListener::DEBUG
        );
    }

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
        $this->notifyListeners(
          sprintf(
            'Started thread #%d (%s%d sibling(s)) for execution #%d of workflow "%s" (version %d).',

            $threadId,
            $parentId != null ? 'parent: ' . $parentId . ', ' : '',
            $numSiblings,
            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          ),
          ezcWorkflowExecutionListener::DEBUG
        );
    }

    /**
     * Called after a thread has ended.
     *
     * @param ezcWorkflowExecution $execution
     * @param int                  $threadId
     */
    public function afterThreadEnded( ezcWorkflowExecution $execution, $threadId )
    {
        $this->notifyListeners(
          sprintf(
            'Ended thread #%d for execution #%d of workflow "%s" (version %d).',

            $threadId,
            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          ),
          ezcWorkflowExecutionListener::DEBUG
        );
    }

    /**
     * Called after a variable has been set.
     *
     * @param ezcWorkflowExecution $execution
     * @param string               $variableName
     * @param mixed                $value
     */
    public function afterVariableSet( ezcWorkflowExecution $execution, $variableName, $value )
    {
        $this->notifyListeners(
          sprintf(
            'Set variable "%s" to "%s" for execution #%d of workflow "%s" (version %d).',

            $variableName,
            ezcWorkflowUtil::variableToString( $value ),
            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          ),
          ezcWorkflowExecutionListener::DEBUG
        );
    }

    /**
     * Called after a variable has been unset.
     *
     * @param ezcWorkflowExecution $execution
     * @param string               $variableName
     */
    public function afterVariableUnset( ezcWorkflowExecution $execution, $variableName )
    {
        $this->notifyListeners(
          sprintf(
            'Unset variable "%s" for execution #%d of workflow "%s" (version %d).',

            $variableName,
            $execution->getId(),
            $execution->workflow->name,
            $execution->workflow->version
          ),
          ezcWorkflowExecutionListener::DEBUG
        );
    }
}
?>
