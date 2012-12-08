<?php
/**
 * File containing the ezcWorkflowVisitorReset class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An implementation of the ezcWorkflowVisitor interface that
 * resets all the nodes of a workflow.
 *
 * This visitor should not be used directly but will be used by the
 * reset() method on the workflow.
 *
 * <code>
 * <?php
 * $workflow->reset();
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowVisitorReset extends ezcWorkflowVisitor
{
    /**
     * Perform the visit.
     *
     * @param ezcWorkflowVisitable $visitable
     */
    protected function doVisit( ezcWorkflowVisitable $visitable )
    {
        if ( $visitable instanceof ezcWorkflowNode )
        {
            $visitable->initState();
        }
    }
}
?>
