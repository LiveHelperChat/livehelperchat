<?php
/**
 * File containing the ezcWorkflowNodeConditionalBranch class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for nodes that conditionally branch multiple threads of
 * execution.
 *
 * Most implementations only need to set the conditions for proper functioning.
 *
 * @package Workflow
 * @version 1.4.1
 */
abstract class ezcWorkflowNodeConditionalBranch extends ezcWorkflowNodeBranch
{
    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to have. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minConditionalOutNodes = false;

    /**
     * Constraint: The minimum number of conditional outgoing nodes this node
     * has to activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $minActivatedConditionalOutNodes = false;

    /**
     * Constraint: The maximum number of conditional outgoing nodes this node
     * may activate. Set to false to disable this constraint.
     *
     * @var integer
     */
    protected $maxActivatedConditionalOutNodes = false;

    /**
     * Holds the conditions of the out nodes.
     *
     * The key is the position of the out node in the array of out nodes.
     *
     * @var array( 'condition' => array( 'int' => ezcWorkflowCondtion ) )
     */
    protected $configuration = array(
      'condition' => array(),
      'else' => array()
    );

    /**
     * Adds the conditional outgoing node $outNode to this node with the
     * condition $condition. Optionally, an $else node can be specified that is
     * activated when the $condition evaluates to false.
     *
     * @param ezcWorkflowCondition $condition
     * @param ezcWorkflowNode      $outNode
     * @param ezcWorkflowNode      $else
     * @return ezcWorkflowNode
     */
    public function addConditionalOutNode( ezcWorkflowCondition $condition, ezcWorkflowNode $outNode, ezcWorkflowNode $else = null )
    {
        $this->addOutNode( $outNode );
        $this->configuration['condition'][ezcWorkflowUtil::findObject( $this->outNodes, $outNode )] = $condition;

        if ( !is_null( $else ) )
        {
            $this->addOutNode( $else );

            $key = ezcWorkflowUtil::findObject( $this->outNodes, $else );
            $this->configuration['condition'][$key] = new ezcWorkflowConditionNot( $condition );
            $this->configuration['else'][$key] = true;
        }

        return $this;
    }

    /**
     * Returns the condition for a conditional outgoing node
     * and false if the passed not is not a (unconditional)
     * outgoing node of this node.
     *
     * @param  ezcWorkflowNode $node
     * @return ezcWorkflowCondition
     * @ignore
     */
    public function getCondition( ezcWorkflowNode $node )
    {
        $keys    = array_keys( $this->outNodes );
        $numKeys = count( $keys );

        for ( $i = 0; $i < $numKeys; $i++ )
        {
            if ( $this->outNodes[$keys[$i]] === $node )
            {
                if ( isset( $this->configuration['condition'][$keys[$i]] ) )
                {
                    return $this->configuration['condition'][$keys[$i]];
                }
                else
                {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Returns true when the $node belongs to an ELSE condition.
     *
     * @param ezcWorkflowNode $node
     * @return bool
     * @ignore
     */
    public function isElse( ezcWorkflowNode $node )
    {
        return isset( $this->configuration['else'][ezcWorkflowUtil::findObject( $this->outNodes, $node )] );
    }

    /**
     * Evaluates all the conditions, checks the constraints and activates any nodes that have
     * passed through both checks and condition evaluation.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        $keys                            = array_keys( $this->outNodes );
        $numKeys                         = count( $keys );
        $nodesToStart                    = array();
        $numActivatedConditionalOutNodes = 0;

        if ( $this->maxActivatedConditionalOutNodes !== false )
        {
            $maxActivatedConditionalOutNodes = $this->maxActivatedConditionalOutNodes;
        }
        else
        {
            $maxActivatedConditionalOutNodes = $numKeys;
        }

        for ( $i = 0; $i < $numKeys && $numActivatedConditionalOutNodes <= $maxActivatedConditionalOutNodes; $i++ )
        {
            if ( isset( $this->configuration['condition'][$keys[$i]] ) )
            {
                // Conditional outgoing node.
                if ( $this->configuration['condition'][$keys[$i]]->evaluate( $execution->getVariables() ) )
                {
                    $nodesToStart[] = $this->outNodes[$keys[$i]];
                    $numActivatedConditionalOutNodes++;
                }
            }
            else
            {
                // Unconditional outgoing node.
                $nodesToStart[] = $this->outNodes[$keys[$i]];
            }
        }

        if ( $this->minActivatedConditionalOutNodes !== false && $numActivatedConditionalOutNodes < $this->minActivatedConditionalOutNodes )
        {
            throw new ezcWorkflowExecutionException(
              'Node activates less conditional outgoing nodes than required.'
            );
        }

        return $this->activateOutgoingNodes( $execution, $nodesToStart );
    }

    /**
     * Checks this node's constraints.
     *
     * @throws ezcWorkflowInvalidWorkflowException if the constraints of this node are not met.
     */
    public function verify()
    {
        parent::verify();

        $numConditionalOutNodes = count( $this->configuration['condition'] );

        if ( $this->minConditionalOutNodes !== false && $numConditionalOutNodes < $this->minConditionalOutNodes )
        {
            throw new ezcWorkflowInvalidWorkflowException(
              'Node has less conditional outgoing nodes than required.'
            );
        }
    }
}
?>
