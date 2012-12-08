<?php
/**
 * File containing the ezcWorkflowNodeMerge class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for nodes that merge multiple threads of execution.
 *
 * @package Workflow
 * @version 1.4.1
 */
abstract class ezcWorkflowNodeMerge extends ezcWorkflowNode
{
    /**
     * Constraint: The minimum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $minInNodes = 2;

    /**
     * Constraint: The maximum number of incoming nodes this node has to have
     * to be valid.
     *
     * @var integer
     */
    protected $maxInNodes = false;

    /**
     * The state of this node.
     *
     * @var array
     */
    protected $state;

    /**
     * Prepares this node for activation.
     *
     * @param ezcWorkflowExecution $execution
     * @param int $threadId
     * @throws ezcWorkflowExecutionException
     */
    protected function prepareActivate( ezcWorkflowExecution $execution, $threadId = 0 )
    {
        $parentThreadId = $execution->getParentThreadId( $threadId );

        if ( $this->state['siblings'] == -1 )
        {
            $this->state['siblings'] = $execution->getNumSiblingThreads( $threadId );
        }
        else
        {
            foreach ( $this->state['threads'] as $oldThreadId )
            {
                if ( $parentThreadId != $execution->getParentThreadId( $oldThreadId ) )
                {
                    throw new ezcWorkflowExecutionException(
                      'Cannot synchronize threads that were started by different branches.'
                    );
                }
            }
        }

        $this->state['threads'][] = $threadId;
    }

    /**
     * Performs the merge by ending the incoming threads and
     * activating the outgoing node.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     */
    protected function doMerge( ezcWorkflowExecution $execution )
    {
        foreach ( $this->state['threads'] as $threadId )
        {
            $execution->endThread( $threadId );
        }

        $this->activateNode( $execution, $this->outNodes[0] );
        $this->initState();

        return parent::execute( $execution );
    }

    /**
     * Initializes the state of this node.
     *
     * @ignore
     */
    public function initState()
    {
        parent::initState();

        $this->state = array( 'threads' => array(), 'siblings' => -1 );
    }
}
?>
