<?php
/**
 * File containing the ezcWorkflowNodeLoop class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The Loop node type is a special type of conditional branch node that has two
 * incoming nodes instead of just one. It is used to conveniently express loops.
 *
 * Incoming nodes: 2..*
 * Outgoing nodes: 2..*
 *
 * The example below shows the equivalent of a for-loop that iterates the
 * variable i from 1 to 10:
 *
 * <code>
 * <?php
 * $workflow = new ezcWorkflow( 'IncrementingLoop' );
 *
 * $set      = new ezcWorkflowNodeVariableSet( array( 'i' => 1 ) );
 * $step     = new ezcWorkflowNodeVariableIncrement( 'i' );
 * $break    = new ezcWorkflowConditionVariable( 'i', new ezcWorkflowConditionIsEqual( 10 ) );
 * $continue = new ezcWorkflowConditionVariable( 'i', new ezcWorkflowConditionIsLessThan( 10 ) );
 *
 * $workflow->startNode->addOutNode( $set );
 *
 * $loop = new ezcWorkflowNodeLoop;
 * $loop->addInNode( $set );
 * $loop->addInNode( $step );
 *
 * $loop->addConditionalOutNode( $continue, $step );
 * $loop->addConditionalOutNode( $break, $workflow->endNode );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeLoop extends ezcWorkflowNodeConditionalBranch
{
    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minInNodes = 2;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxInNodes = false;

    /**
     * Constraint: The minimum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minOutNodes = 2;

    /**
     * Constraint: The maximum number of outgoing nodes this node has to have
     * to be valid. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxOutNodes = false;

    /**
     * Whether or not to start a new thread for a branch.
     *
     * @var bool
     */
    protected $startNewThreadForBranch = false;
}
?>
