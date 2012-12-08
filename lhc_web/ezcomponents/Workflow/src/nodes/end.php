<?php
/**
 * File containing the ezcWorkflowNodeEnd class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeEnd class represents an end node of a workflow.
 *
 * A workflow must have at least one end node. The execution of the workflow ends
 * when an end node is reached.
 * Creating an object of the ezcWorkflow class automatically creates a default end node for the new
 * workflow. It can be accessed through the getEndNode() method.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 0
 *
 * Example:
 * <code>
 * <?php
 * $workflow = new ezcWorkflow( 'Test' );
 * // build up your workflow here... result in $node
 * $node = ...
 * $workflow->startNode->addOutNode( ... some other node here ... );
 * $node->addOutNode( $workflow->endNode );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeEnd extends ezcWorkflowNode
{
    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $minOutNodes = 0;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $maxOutNodes = 0;

    /**
     * Ends the execution of this workflow.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        $execution->end( $this );

        return parent::execute( $execution );
    }
}
?>
