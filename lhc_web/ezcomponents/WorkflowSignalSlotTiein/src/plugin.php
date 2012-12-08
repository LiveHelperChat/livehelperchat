<?php
/**
 * File containing the ezcWorkflowSignalSlotPlugin class.
 *
 * @package WorkflowSignalSlotTiein
 * @version 1.0
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A workflow execution engine plugin that emits signals.
 *
 * @property ezcWorkflowSignalSlotPluginOptions $options
 * @property ezcSignalCollection                $signals
 *
 * @package WorkflowSignalSlotTiein
 * @version 1.0
 */
class ezcWorkflowSignalSlotPlugin extends ezcWorkflowExecutionPlugin
{
    /**
     * Properties. 
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * @var ezcSignalCollection
     */
    protected $signals;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->properties['options'] = new ezcWorkflowSignalSlotPluginOptions;
        $this->properties['signals'] = new ezcSignalCollection;
    }

    /**
     * Property get access.
     *
     * @param string $propertyName
     * @return mixed
     * @throws ezcBasePropertyNotFoundException
     *         If the given property could not be found.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property set access.
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @throws ezcBasePropertyNotFoundException
     *         If the given property could not be found.
     * @throws ezcBaseValueException 
     *         If the value for the property options is not an ezcWorkflowSignalSlotPluginOptions object.
     * @throws ezcBaseValueException 
     *         If the value for the property signals is not an ezcSignalCollection object.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'options':
                if ( !( $propertyValue instanceof ezcWorkflowSignalSlotPluginOptions ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWorkflowSignalSlotPluginOptions'
                    );
                }
                break;

            case 'signals':
                if ( !( $propertyValue instanceof ezcSignalCollection ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcSignalCollection'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access. 
     * 
     * @param string $propertyName 
     * @return bool
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }

    /**
     * Called after an execution has been started.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionStarted( ezcWorkflowExecution $execution )
    {
        $this->properties['signals']->emit( $this->options['afterExecutionStarted'], $execution );
    }

    /**
     * Called after an execution has been suspended.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionSuspended( ezcWorkflowExecution $execution )
    {
        $this->properties['signals']->emit( $this->options['afterExecutionSuspended'], $execution );
    }

    /**
     * Called after an execution has been resumed.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionResumed( ezcWorkflowExecution $execution )
    {
        $this->properties['signals']->emit( $this->options['afterExecutionResumed'], $execution );
    }

    /**
     * Called after an execution has been cancelled.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionCancelled( ezcWorkflowExecution $execution )
    {
        $this->properties['signals']->emit( $this->options['afterExecutionCancelled'], $execution );
    }

    /**
     * Called after an execution has successfully ended.
     *
     * @param ezcWorkflowExecution $execution
     */
    public function afterExecutionEnded( ezcWorkflowExecution $execution )
    {
        $this->properties['signals']->emit( $this->options['afterExecutionEnded'], $execution );
    }

    /**
     * Called before a node is activated.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     * @return bool true, when the node should be activated, false otherwise
     */
    public function beforeNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $return = new ezcWorkflowSignalSlotReturnValue;

        $this->properties['signals']->emit( $this->options['beforeNodeActivated'], $execution, $node, $return );

        return $return->value;
    }

    /**
     * Called after a node has been activated.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $this->properties['signals']->emit( $this->options['afterNodeActivated'], $execution, $node );
    }

    /**
     * Called after a node has been executed.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeExecuted( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $this->properties['signals']->emit( $this->options['afterNodeExecuted'], $execution, $node );
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
        $this->properties['signals']->emit( $this->options['afterThreadStarted'], $execution, $threadId, $parentId, $numSiblings );
    }

    /**
     * Called after a thread has ended.
     *
     * @param ezcWorkflowExecution $execution
     * @param int                  $threadId
     */
    public function afterThreadEnded( ezcWorkflowExecution $execution, $threadId )
    {
        $this->properties['signals']->emit( $this->options['afterThreadEnded'], $execution, $threadId );
    }

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
        $return = new ezcWorkflowSignalSlotReturnValue( $value );

        $this->properties['signals']->emit( $this->options['beforeVariableSet'], $execution, $variableName, $value, $return );

        return $return->value;
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
        $this->properties['signals']->emit( $this->options['afterVariableSet'], $execution, $variableName, $value );
    }

    /**
     * Called before a variable is unset.
     *
     * @param  ezcWorkflowExecution $execution
     * @param  string               $variableName
     * @return bool true, when the variable should be unset, false otherwise
     */
    public function beforeVariableUnset( ezcWorkflowExecution $execution, $variableName )
    {
        $return = new ezcWorkflowSignalSlotReturnValue;

        $this->properties['signals']->emit( $this->options['beforeVariableUnset'], $execution, $variableName, $return );

        return $return->value;
    }

    /**
     * Called after a variable has been unset.
     *
     * @param ezcWorkflowExecution $execution
     * @param string               $variableName
     */
    public function afterVariableUnset( ezcWorkflowExecution $execution, $variableName )
    {
        $this->properties['signals']->emit( $this->options['afterVariableUnset'], $execution, $variableName );
    }
}
?>
