<?php
/**
 * File containing the ezcWorkflowNodeStart class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeStart class represents the one and only
 * tart node of a workflow. The execution of the workflow starts here.
 *
 * Creating an object of the ezcWorkflow class automatically creates the start node
 * for the new workflow. It can be accessed through the $startNode property of the
 * workflow.
 *
 * Incoming nodes: 0
 * Outgoing nodes: 1
 *
 * Example:
 * <code>
 * <?php
 * $workflow = new ezcWorkflow( 'Test' );
 * $workflow->startNode->addOutNode( ....some other node here .. );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeStart extends ezcWorkflowNode
{
    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $minInNodes = 0;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $maxInNodes = 0;

    /**
     * Activates the sole output node.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        $this->outNodes[0]->activate(
          $execution,
          $this,
          $execution->startThread()
        );

        return parent::execute( $execution );
    }
}
?>
