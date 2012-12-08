<?php
/**
 * File containing the ezcWorkflowExecutionNonInteractive class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Workflow execution engine for non-interactive workflows.
 *
 * This workflow execution engine can only execute workflows that do not have
 * any Input and/or SubWorkflow nodes.
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowExecutionNonInteractive extends ezcWorkflowExecution
{
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
        if ( $val instanceof ezcWorkflow && ( $val->isInteractive() || $val->hasSubWorkflows() ) )
        {
            throw new ezcWorkflowExecutionException(
              'This executer can only execute workflows that have no Input and SubWorkflow nodes.'
            );
        }

        return parent::__set( $propertyName, $val );
    }

    /**
     * Start workflow execution.
     *
     * @param  integer $parentId
     */
    protected function doStart( $parentId )
    {
    }

    /**
     * Suspend workflow execution.
     */
    protected function doSuspend()
    {
    }

    /**
     * Resume workflow execution.
     */
    protected function doResume()
    {
    }

    /**
     * End workflow execution.
     */
    protected function doEnd()
    {
    }

    /**
     * Returns a new execution object for a sub workflow.
     *
     * @param  int $id
     * @return ezcWorkflowExecution
     */
    protected function doGetSubExecution( $id = null )
    {
    }
}
?>
