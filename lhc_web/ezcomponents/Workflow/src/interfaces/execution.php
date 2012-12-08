<?php
/**
 * File containing the ezcWorkflowExecution class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for workflow execution engines.
 *
 * ezcWorkflowExecution provides all functionality necessary to execute
 * a workflow. However, it does not provide functionality to make the
 * execution of a workflow persistent and hence usuable over more than
 * one PHP run.
 *
 * Implementations must implement the do* methods and provide the means
 * to store the execution data to a persistent medium.
 *
 * @property ezcWorkflowDefinitonStorage $definitionStorage
 *           The definition handler used to fetch subworkflows if needed.
 * @property ezcWorkflow                 $workflow
 *           The workflow being executed.
 *
 * @package Workflow
 * @version 1.4.1
 * @mainclass
 */
abstract class ezcWorkflowExecution
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'definitionStorage' => null,
      'workflow' => null
    );

    /**
     * Execution ID.
     *
     * @var integer
     */
    protected $id;

    /**
     * Nodes of the workflow being executed that are activated.
     *
     * @var ezcWorkflowNode[]
     */
    protected $activatedNodes = array();

    /**
     * Number of activated nodes.
     *
     * @var integer
     */
    protected $numActivatedNodes = 0;

    /**
     * Number of activated end nodes.
     *
     * @var integer
     */
    protected $numActivatedEndNodes = 0;

    /**
     * Nodes of the workflow that started a new thread of execution.
     *
     * @var array
     */
    protected $threads = array();

    /**
     * Sequence for thread ids.
     *
     * @var integer
     */
    protected $nextThreadId = 0;

    /**
     * Flag that indicates whether or not this execution has been cancelled.
     *
     * @var bool
     */
    protected $cancelled;

    /**
     * Flag that indicates whether or not this execution has ended.
     *
     * @var bool
     */
    protected $ended;

    /**
     * Flag that indicates whether or not this execution has been resumed.
     *
     * @var bool
     */
    protected $resumed;

    /**
     * Flag that indicates whether or not this execution has been suspended.
     *
     * @var bool
     */
    protected $suspended;

    /**
     * Plugins registered for this execution.
     *
     * @var array
     */
    protected $plugins = array();

    /**
     * Workflow variables.
     *
     * @var array
     */
    protected $variables = array();

    /**
     * Workflow variables the execution is waiting for.
     *
     * @var array
     */
    protected $waitingFor = array();

    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     *
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
            case 'workflow':
                return $this->properties[$propertyName];
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBaseValueException
     *         If a the value for the property definitionStorage is not an
     *         instance of ezcWorkflowDefinitionStorage.
     * @throws ezcBaseValueException
     *         If a the value for the property workflow is not an instance of
     *         ezcWorkflow.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
                if ( !( $val instanceof ezcWorkflowDefinitionStorage ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcWorkflowDefinitionStorage' );
                }

                $this->properties['definitionStorage'] = $val;

                return;

            case 'workflow':
                if ( !( $val instanceof ezcWorkflow ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcWorkflow' );
                }

                $this->properties['workflow'] = $val;

                return;
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
            case 'workflow':
                return true;
        }

        return false;
    }

    /**
     * Starts the execution of the workflow and returns the execution id.
     *
     * $parentId is used to specify the execution id of the parent workflow
     * when executing subworkflows. It should not be used when manually
     * starting workflows.
     *
     * Calls doStart() right before the first node is activated.
     *
     * @param int $parentId
     * @return mixed Execution ID if the workflow has been suspended,
     *               null otherwise.
     * @throws ezcWorkflowExecutionException
     *         If no workflow has been set up for execution.
     */
    public function start( $parentId = null )
    {
        if ( $this->workflow === null )
        {
            throw new ezcWorkflowExecutionException(
              'No workflow has been set up for execution.'
            );
        }

        $this->cancelled = false;
        $this->ended     = false;
        $this->resumed   = false;
        $this->suspended = false;

        $this->doStart( $parentId );
        $this->loadFromVariableHandlers();

        foreach ( $this->plugins as $plugin )
        {
            $plugin->afterExecutionStarted( $this );
        }

        // Start workflow execution by activating the start node.
        $this->workflow->startNode->activate( $this );

        // Continue workflow execution until there are no more
        // activated nodes.
        $this->execute();

        // Return execution ID if the workflow has been suspended.
        if ( $this->isSuspended() )
        {
            return (int)$this->id;
        }
    }

    /**
     * Suspends workflow execution.
     *
     * This method is usually called by the execution environment when there are no more
     * more activated nodes that can be executed. This is commonly the case with input
     * nodes waiting for input.
     *
     * This method calls doSuspend() before calling saveToVariableHandlers() allowing
     * reimplementations to save variable and node information.
     *
     * @ignore
     */
    public function suspend()
    {
        $this->cancelled = false;
        $this->ended     = false;
        $this->resumed   = false;
        $this->suspended = true;

        $this->saveToVariableHandlers();

        $keys     = array_keys( $this->variables );
        $count    = count( $keys );
        $handlers = $this->workflow->getVariableHandlers();

        for ( $i = 0; $i < $count; $i++ )
        {
            if ( isset( $handlers[$keys[$i]] ) )
            {
                unset( $this->variables[$keys[$i]] );
            }
        }

        $this->doSuspend();

        foreach ( $this->plugins as $plugin )
        {
            $plugin->afterExecutionSuspended( $this );
        }
    }

    /**
     * Resumes workflow execution of a suspended workflow.
     *
     * $executionId is the id of the execution to resume. $inputData is an
     * associative array of the format array( 'variable name' => value ) that should
     * contain new workflow variable data required to resume execution.
     *
     * Calls do doResume() before the variables are loaded using the variable handlers.
     *
     * @param array   $inputData    The new input data.
     * @throws ezcWorkflowInvalidInputException if the input given does not match the expected data.
     * @throws ezcWorkflowExecutionException if there is no prior ID for this execution.
     */
    public function resume( array $inputData = array() )
    {
        if ( $this->id === null )
        {
            throw new ezcWorkflowExecutionException(
              'No execution id given.'
            );
        }

        $this->cancelled = false;
        $this->ended     = false;
        $this->resumed   = true;
        $this->suspended = false;

        $this->doResume();
        $this->loadFromVariableHandlers();

        $errors = array();

        foreach ( $inputData as $variableName => $value )
        {
            if ( isset( $this->waitingFor[$variableName] ) )
            {
                if ( $this->waitingFor[$variableName]['condition']->evaluate( $value ) )
                {
                    $this->setVariable( $variableName, $value );
                    unset( $this->waitingFor[$variableName] );
                }
                else
                {
                    $errors[$variableName] = (string)$this->waitingFor[$variableName]['condition'];
                }
            }
        }

        if ( !empty( $errors ) )
        {
            throw new ezcWorkflowInvalidInputException( $errors );
        }

        foreach ( $this->plugins as $plugin )
        {
            $plugin->afterExecutionResumed( $this );
        }

        $this->execute();

        // Return execution ID if the workflow has been suspended.
        if ( $this->isSuspended() )
        {
            // @codeCoverageIgnoreStart
            return $this->id;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Cancels workflow execution with the node $endNode.
     *
     * @param ezcWorkflowNode $node
     */
    public function cancel( ezcWorkflowNode $node = null )
    {
        if ( $node !== null )
        {
            foreach ( $this->plugins as $plugin )
            {
                $plugin->afterNodeExecuted( $this, $node );
            }
        }

        $this->activatedNodes    = array();
        $this->numActivatedNodes = 0;
        $this->waitingFor        = array();

        if ( count( $this->workflow->finallyNode->getOutNodes() ) > 0 )
        {
            $this->workflow->finallyNode->activate( $this );
            $this->execute();
        }

        $this->cancelled = true;
        $this->ended     = false;

        $this->end( $node );
        $this->doEnd();
    }

    /**
     * Ends workflow execution with the node $endNode.
     *
     * End nodes must call this method to end the execution.
     *
     * @param ezcWorkflowNode $node
     * @ignore
     */
    public function end( ezcWorkflowNode $node = null )
    {
        if ( !$this->cancelled )
        {
            if ( $node !== null )
            {
                foreach ( $this->plugins as $plugin )
                {
                    $plugin->afterNodeExecuted( $this, $node );
                }
            }

            $this->ended     = true;
            $this->resumed   = false;
            $this->suspended = false;

            $this->doEnd();
            $this->saveToVariableHandlers();

            if ( $node !== null )
            {
                $this->endThread( $node->getThreadId() );

                foreach ( $this->plugins as $plugin )
                {
                    $plugin->afterExecutionEnded( $this );
                }
            }
        }
        else
        {
            foreach ( $this->plugins as $plugin )
            {
                $plugin->afterExecutionCancelled( $this );
            }
        }
    }

    /**
     * The workflow engine's main execution loop. It is started by start() and
     * resume().
     *
     * @ignore
     */
    protected function execute()
    {
        // Try to execute nodes while there are executable nodes on the stack.
        do
        {
            // Flag that indicates whether a node has been executed during the
            // current iteration of the loop.
            $executed = false;

            // Iterate the stack of activated nodes.
            foreach ( $this->activatedNodes as $key => $node )
            {
                // Only try to execute a node if the execution of the
                // workflow instance has not ended yet.
                if ( $this->cancelled && $this->ended )
                {
                    // @codeCoverageIgnoreStart
                    break;
                    // @codeCoverageIgnoreEnd
                }

                // The current node is an end node but there are still
                // activated nodes on the stack.
                if ( $node  instanceof ezcWorkflowNodeEnd &&
                     !$node instanceof ezcWorkflowNodeCancel &&
                     $this->numActivatedNodes != $this->numActivatedEndNodes )
                {
                    continue;
                }

                // Execute the current node and check whether it finished
                // executing.
                if ( $node->execute( $this ) )
                {
                    // Remove current node from the stack of activated
                    // nodes.
                    unset( $this->activatedNodes[$key] );
                    $this->numActivatedNodes--;

                    // Notify plugins that the node has been executed.
                    if ( !$this->cancelled && !$this->ended )
                    {
                        foreach ( $this->plugins as $plugin )
                        {
                            $plugin->afterNodeExecuted( $this, $node );
                        }
                    }

                    // Toggle flag (see above).
                    $executed = true;
                }
            }
        }
        while ( !empty( $this->activatedNodes ) && $executed );

        // The stack of activated nodes is not empty but at the moment none of
        // its nodes can be executed.
        if ( !$this->cancelled && !$this->ended )
        {
            $this->suspend();
        }
    }

    /**
     * Activates a node and returns true if it was activated, false if not.
     *
     * The node will only be activated if the node is executable.
     * See {@link ezcWorkflowNode::isExecutable()}.
     *
     * @param ezcWorkflowNode $node
     * @param bool            $notifyPlugins
     * @return bool
     * @ignore
     */
    public function activate( ezcWorkflowNode $node, $notifyPlugins = true )
    {
        // Only activate the node when
        //  - the execution of the workflow has not been cancelled,
        //  - the node is ready to be activated,
        //  - and the node is not already activated.
        if ( $this->cancelled ||
             !$node->isExecutable() ||
             ezcWorkflowUtil::findObject( $this->activatedNodes, $node ) !== false )
        {
            return false;
        }

        $activateNode = true;

        foreach ( $this->plugins as $plugin )
        {
            $activateNode = $plugin->beforeNodeActivated( $this, $node );

            if ( !$activateNode )
            {
            // @codeCoverageIgnoreStart
                break;
            // @codeCoverageIgnoreEnd
            }
        }

        if ( $activateNode )
        {
            // Add node to list of activated nodes.
            $this->activatedNodes[] = $node;
            $this->numActivatedNodes++;

            if ( $node instanceof ezcWorkflowNodeEnd )
            {
                $this->numActivatedEndNodes++;
            }

            if ( $notifyPlugins )
            {
                foreach ( $this->plugins as $plugin )
                {
                    $plugin->afterNodeActivated( $this, $node );
                }
            }

            return true;
        }
        else
        {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Adds a variable that an (input) node is waiting for.
     *
     * @param ezcWorkflowNode $node
     * @param string $variableName
     * @param ezcWorkflowCondition $condition
     * @ignore
     */
    public function addWaitingFor( ezcWorkflowNode $node, $variableName, ezcWorkflowCondition $condition )
    {
        if ( !isset( $this->waitingFor[$variableName] ) )
        {
            $this->waitingFor[$variableName] = array(
              'node' => $node->getId(),
              'condition' => $condition
            );
        }
    }

    /**
     * Returns the variables that (input) nodes are waiting for.
     *
     * @return array
     * @ignore
     */
    public function getWaitingFor()
    {
        return $this->waitingFor;
    }

    /**
     * Start a new thread and returns the id of the new thread.
     *
     * @param int $parentId The id of the parent thread.
     * @param int $numSiblings The number of threads that are started by the same node.
     * @return int
     * @ignore
     */
    public function startThread( $parentId = null, $numSiblings = 1 )
    {
        if ( !$this->cancelled )
        {
            $this->threads[$this->nextThreadId] = array(
              'parentId' => $parentId,
              'numSiblings' => $numSiblings
            );

            foreach ( $this->plugins as $plugin )
            {
                $plugin->afterThreadStarted( $this, $this->nextThreadId, $parentId, $numSiblings );
            }

            return $this->nextThreadId++;
        }

        return false;
    }

    /**
     * Ends the thread with id $threadId
     *
     * @param  integer $threadId
     * @ignore
     */
    public function endThread( $threadId )
    {
        if ( isset( $this->threads[$threadId] ) )
        {
            unset( $this->threads[$threadId] );

            foreach ( $this->plugins as $plugin )
            {
                $plugin->afterThreadEnded( $this, $threadId );
            }
        }
        else
        {
            throw new ezcWorkflowExecutionException(
              sprintf(
                'There is no thread with id #%d.',
                $threadId
              )
            );
        }
    }

    /**
     * Returns a new execution object for a sub workflow.
     *
     * If this method is used to resume a subworkflow you must provide
     * the execution id through $id.
     *
     * If $interactive is false an ezcWorkflowExecutionNonInteractive
     * will be returned.
     *
     * This method can be used by nodes implementing sub-workflows
     * to get a new execution environment for the subworkflow.
     *
     * @param  int $id
     * @param  bool $interactive
     * @return ezcWorkflowExecution
     * @ignore
     */
    public function getSubExecution( $id = null, $interactive = true )
    {
        if ( $interactive )
        {
            $execution = $this->doGetSubExecution( $id );
        }
        else
        {
            $execution = new ezcWorkflowExecutionNonInteractive;
        }

        foreach ( $this->plugins as $plugin )
        {
            $execution->addPlugin( $plugin );
        }

        return $execution;
    }

    /**
     * Returns the number of siblings for a given thread.
     *
     * @param  int $threadId The id of the thread for which to return the number of siblings.
     * @return int
     * @ignore
     */
    public function getNumSiblingThreads( $threadId )
    {
        if ( isset( $this->threads[$threadId] ) )
        {
            return $this->threads[$threadId]['numSiblings'];
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the id of the parent thread for a given thread.
     *
     * @param  int $threadId The id of the thread for which to return the parent thread id.
     * @return int
     * @ignore
     */
    public function getParentThreadId( $threadId )
    {
        if ( isset( $this->threads[$threadId] ) )
        {
            return $this->threads[$threadId]['parentId'];
        }
        else
        {
            return false;
        }
    }

    /**
     * Adds a plugin to this execution.
     *
     * @param ezcWorkflowExecutionPlugin $plugin
     * @return bool true when the plugin was added, false otherwise.
     */
    public function addPlugin( ezcWorkflowExecutionPlugin $plugin )
    {
        $pluginClass = get_class( $plugin );

        if ( !isset( $this->plugins[$pluginClass] ) )
        {
            $this->plugins[$pluginClass] = $plugin;

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Removes a plugin from this execution.
     *
     * @param ezcWorkflowExecutionPlugin $plugin
     * @return bool true when the plugin was removed, false otherwise.
     */
    public function removePlugin( ezcWorkflowExecutionPlugin $plugin )
    {
        $pluginClass = get_class( $plugin );

        if ( isset( $this->plugins[$pluginClass] ) )
        {
            unset( $this->plugins[$pluginClass] );

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Adds a listener to this execution.
     *
     * @param ezcWorkflowExecutionListener $listener
     * @return bool true when the listener was added, false otherwise.
     */
    public function addListener( ezcWorkflowExecutionListener $listener )
    {
        if ( !isset( $this->plugins['ezcWorkflowExecutionListenerPlugin'] ) )
        {
            $this->addPlugin( new ezcWorkflowExecutionListenerPlugin );
        }

        return $this->plugins['ezcWorkflowExecutionListenerPlugin']->addListener( $listener );
    }

    /**
     * Removes a listener from this execution.
     *
     * @param ezcWorkflowExecutionListener $listener
     * @return bool true when the listener was removed, false otherwise.
     */
    public function removeListener( ezcWorkflowExecutionListener $listener )
    {
        if ( isset( $this->plugins['ezcWorkflowExecutionListenerPlugin'] ) )
        {
            return $this->plugins['ezcWorkflowExecutionListenerPlugin']->removeListener( $listener );
        }

        return false;
    }

    /**
     * Returns the activated nodes.
     *
     * @return array
     * @ignore
     */
    public function getActivatedNodes()
    {
        return $this->activatedNodes;
    }

    /**
     * Returns the execution ID.
     *
     * @return int
     * @ignore
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a variable.
     *
     * @param string $variableName
     * @ignore
     */
    public function getVariable( $variableName )
    {
        if ( array_key_exists( $variableName, $this->variables ) )
        {
            return $this->variables[$variableName];
        }
        else
        {
            throw new ezcWorkflowExecutionException(
              sprintf(
                'Variable "%s" does not exist.',
                $variableName
              )
            );
        }
    }

    /**
     * Returns the variables.
     *
     * @return array
     * @ignore
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Checks whether or not a workflow variable has been set.
     *
     * @param string $variableName
     * @return bool true when the variable exists and false otherwise.
     * @ignore
     */
    public function hasVariable( $variableName )
    {
        return array_key_exists( $variableName, $this->variables );
    }

    /**
     * Sets a variable.
     *
     * @param  string $variableName
     * @param  mixed  $value
     * @return mixed the value that the variable has been set to
     * @ignore
     */
    public function setVariable( $variableName, $value )
    {
        foreach ( $this->plugins as $plugin )
        {
            $value = $plugin->beforeVariableSet( $this, $variableName, $value );
        }

        $this->variables[$variableName] = $value;

        foreach ( $this->plugins as $plugin )
        {
            $plugin->afterVariableSet( $this, $variableName, $value );
        }

        return $value;
    }

    /**
     * Sets the variables.
     *
     * @param array $variables
     * @ignore
     */
    public function setVariables( array $variables )
    {
        $this->variables = array();

        foreach ( $variables as $variableName => $value )
        {
            $this->setVariable( $variableName, $value );
        }
    }

    /**
     * Unsets a variable.
     *
     * @param  string $variableName
     * @return true, when the variable has been unset, false otherwise
     * @ignore
     */
    public function unsetVariable( $variableName )
    {
        $unsetVariable = true;

        if ( array_key_exists( $variableName, $this->variables ) )
        {
            foreach ( $this->plugins as $plugin )
            {
                $unsetVariable = $plugin->beforeVariableUnset( $this, $variableName );

                if ( !$unsetVariable )
                {
                    break;
                }
            }

            if ( $unsetVariable )
            {
                unset( $this->variables[$variableName] );

                foreach ( $this->plugins as $plugin )
                {
                    $plugin->afterVariableUnset( $this, $variableName );
                }
            }
        }

        return $unsetVariable;
    }

    /**
     * Returns true when the workflow execution has been cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Returns true when the workflow execution has ended.
     *
     * @return bool
     */
    public function hasEnded()
    {
        return $this->ended;
    }

    /**
     * Returns true when the workflow execution has been resumed.
     *
     * @return bool
     * @ignore
     */
    public function isResumed()
    {
        return $this->resumed;
    }

    /**
     * Returns true when the workflow execution has been suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->suspended;
    }

    /**
     * Loads data from variable handlers and
     * merge it with the current execution data.
     */
    protected function loadFromVariableHandlers()
    {
        foreach ( $this->workflow->getVariableHandlers() as $variableName => $className )
        {
            $object = new $className;
            $this->setVariable( $variableName, $object->load( $this, $variableName ) );
        }
    }

    /**
     * Saves data to execution data handlers.
     */
    protected function saveToVariableHandlers()
    {
        foreach ( $this->workflow->getVariableHandlers() as $variableName => $className )
        {
            if ( isset( $this->variables[$variableName] ) )
            {
                $object = new $className;
                $object->save( $this, $variableName, $this->variables[$variableName] );
            }
        }
    }

    /**
     * Called by start() when workflow execution is initiated.
     *
     * Reimplementations can use this method to store workflow information
     * to a persistent medium when execution is started.
     *
     * @param  integer $parentId
     */
    abstract protected function doStart( $parentId );

    /**
     * Called by suspend() when workflow execution is suspended.
     *
     * Reimplementations can use this method to variable and node information
     * to a persistent medium.
     */
    abstract protected function doSuspend();

    /**
     * Called by resume() when workflow execution is resumed.
     *
     * Reimplementations can use this method to fetch execution
     * data if necessary..
     */
    abstract protected function doResume();

    /**
     * Called by end() when workflow execution is ended.
     *
     * Reimplementations can use this method to remove execution
     * data from the persistent medium.
     */
    abstract protected function doEnd();

    /**
     * Returns a new execution object for a sub workflow.
     *
     * Called by getSubExecution to get a new execution
     * environment for the new execution thread.
     *
     * Reimplementations must return a new execution
     * environment similar to themselves.
     *
     * @param  int $id
     * @return ezcWorkflowExecution
     */
    abstract protected function doGetSubExecution( $id = null );
}
?>
