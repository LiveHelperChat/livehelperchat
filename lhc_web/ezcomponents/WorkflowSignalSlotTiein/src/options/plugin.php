<?php
/**
 * This file contains the ezcWorkflowSignalSlotPluginOptions class.
 *
 * @package WorkflowSignalSlotTiein
 * @version 1.0
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Options class for ezcWorkflowVisitorVisualization.
 *
 * @property string $afterExecutionStarted
 *                  The signal that is emitted for the afterExecutionStarted plugin hook.
 * @property string $afterExecutionSuspended
 *                  The signal that is emitted for the afterExecutionSuspended plugin hook.
 * @property string $afterExecutionResumed
 *                  The signal that is emitted for the afterExecutionResumed plugin hook.
 * @property string $afterExecutionCancelled
 *                  The signal that is emitted for the afterExecutionCancelled plugin hook.
 * @property string $afterExecutionEnded
 *                  The signal that is emitted for the afterExecutionEnded plugin hook.
 * @property string $beforeNodeActivated
 *                  The signal that is emitted for the beforeNodeActivated plugin hook.
 * @property string $afterNodeActivated
 *                  The signal that is emitted for the afterNodeActivated plugin hook.
 * @property string $afterNodeExecuted
 *                  The signal that is emitted for the afterNodeExecuted plugin hook.
 * @property string $afterRolledBackServiceObject
 *                  The signal that is emitted for the afterRolledBackServiceObject plugin hook.
 * @property string $afterThreadStarted
 *                  The signal that is emitted for the afterThreadStarted plugin hook.
 * @property string $afterThreadEnded
 *                  The signal that is emitted for the afterThreadEnded plugin hook.
 * @property string $beforeVariableSet
 *                  The signal that is emitted for the beforeVariableSet plugin hook.
 * @property string $afterVariableSet
 *                  The signal that is emitted for the afterVariableSet plugin hook.
 * @property string $beforeVariableUnset
 *                  The signal that is emitted for the beforeVariableUnset plugin hook.
 * @property string $afterVariableUnset
 *                  The signal that is emitted for the afterVariableUnset plugin hook.
 *
 * @package WorkflowSignalSlotTiein
 * @version 1.0
 */
class ezcWorkflowSignalSlotPluginOptions extends ezcBaseOptions
{
    /**
     * Properties.
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'afterExecutionStarted' => 'afterExecutionStarted',
        'afterExecutionSuspended' => 'afterExecutionSuspended',
        'afterExecutionResumed' => 'afterExecutionResumed',
        'afterExecutionCancelled' => 'afterExecutionCancelled',
        'afterExecutionEnded' => 'afterExecutionEnded',
        'beforeNodeActivated' => 'beforeNodeActivated',
        'afterNodeActivated' => 'afterNodeActivated',
        'afterNodeExecuted' => 'afterNodeExecuted',
        'afterRolledBackServiceObject' => 'afterRolledBackServiceObject',
        'afterThreadStarted' => 'afterThreadStarted',
        'afterThreadEnded' => 'afterThreadEnded',
        'beforeVariableSet' => 'beforeVariableSet',
        'afterVariableSet' => 'afterVariableSet',
        'beforeVariableUnset' => 'beforeVariableUnset',
        'afterVariableUnset' => 'afterVariableUnset',
    );

    /**
     * Property write access.
     * 
     * @param string $propertyName  Name of the property.
     * @param mixed  $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'afterExecutionStarted':
            case 'afterExecutionSuspended':
            case 'afterExecutionResumed':
            case 'afterExecutionCancelled':
            case 'afterExecutionEnded':
            case 'beforeNodeActivated':
            case 'afterNodeActivated':
            case 'afterNodeExecuted':
            case 'afterRolledBackServiceObject':
            case 'afterThreadStarted':
            case 'afterThreadEnded':
            case 'beforeVariableSet':
            case 'afterVariableSet':
            case 'beforeVariableUnset':
            case 'afterVariableUnset':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}
?>
