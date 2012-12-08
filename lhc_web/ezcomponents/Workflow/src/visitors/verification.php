<?php
/**
 * File containing the ezcWorkflowVisitorVerification class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An implementation of the ezcWorkflowVisitor interface that
 * verifies a workflow specification.
 *
 * This visitor should not be used directly but will be used by the
 * verify() method on the workflow.
 *
 * <code>
 * <?php
 * $workflow->verify();
 * ?>
 * </code>
 *
 * The verifier checks that:
 * - there is only one start node
 * - there is only one finally node
 * - each node satisfies the constraints of the respective node type
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowVisitorVerification extends ezcWorkflowVisitor
{
    /**
     * Holds the number of start nodes encountered during visiting.
     *
     * @var integer
     */
    protected $numStartNodes = 0;

    /**
     * Holds the number of finally nodes encountered during visiting.
     *
     * @var integer
     */
    protected $numFinallyNodes = 0;

    /**
     * Perform the visit.
     *
     * @param ezcWorkflowVisitable $visitable
     */
    protected function doVisit( ezcWorkflowVisitable $visitable )
    {
        if ( $visitable instanceof ezcWorkflow )
        {
            foreach ( $visitable->nodes as $node )
            {
                if ( $node instanceof ezcWorkflowNodeStart &&
                    !$node instanceof ezcWorkflowNodeFinally )
                {
                    $this->numStartNodes++;

                    if ( $this->numStartNodes > 1 )
                    {
                        throw new ezcWorkflowInvalidWorkflowException(
                          'A workflow may have only one start node.'
                        );
                    }
                }

                if ( $node instanceof ezcWorkflowNodeFinally )
                {
                    $this->numFinallyNodes++;

                    if ( $this->numFinallyNodes > 1 )
                    {
                        throw new ezcWorkflowInvalidWorkflowException(
                          'A workflow may have only one finally node.'
                        );
                    }
                }
            }
        }

        if ( $visitable instanceof ezcWorkflowNode )
        {
            $visitable->verify();
        }
    }
}
?>
