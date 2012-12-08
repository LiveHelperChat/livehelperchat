<?php
/**
 * File containing the ezcWorkflowServiceObject interface.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for service objects that can be attached to
 * ezcWorkflowNodeAction nodes.
 *
 * @package Workflow
 * @version 1.4.1
 */
interface ezcWorkflowServiceObject
{
    /**
     * Executes the business logic of this service object.
     *
     * Implementations can return true if the execution of the
     * service object was successful to resume the workflow and activate
     * the next node.
     *
     * Returning false will cause the workflow to be suspended and the service
     * object to be executed again on a later invokation.
     *
     * @param  ezcWorkflowExecution $execution
     * @return boolean
     */
    public function execute( ezcWorkflowExecution $execution );

    /**
     * Returns a textual representation of this service object.
     *
     * @return string
     */
    public function __toString();
}
?>
