<?php
/**
 * File containing the ezcWorkflowDefinitionStorage interface.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for workflow definition storage handlers.
 *
 * @package Workflow
 * @version 1.4.1
 */
interface ezcWorkflowDefinitionStorage
{
    /**
     * Load a workflow definition by name.
     *
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return ezcWorkflow
     * @throws ezcWorkflowDefinitionStorageException
     */
    public function loadByName( $workflowName, $workflowVersion = 0 );

    /**
     * Save a workflow definition to the database.
     *
     * @param  ezcWorkflow $workflow
     * @throws ezcWorkflowDefinitionStorageException
     */
    public function save( ezcWorkflow $workflow );
}
?>
