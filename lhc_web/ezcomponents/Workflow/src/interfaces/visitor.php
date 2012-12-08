<?php
/**
 * File containing the ezcWorkflowVisitor class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for visitor implementations that want to process
 * a workflow using the Visitor design pattern.
 *
 * visit() is called on each of the nodes in the workflow in a top-down,
 * depth-first fashion.
 *
 * Start the processing of the workflow by calling accept() on the workflow
 * passing the visitor object as the sole parameter.
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowVisitor implements Countable
{
    /**
     * Holds the visited nodes.
     *
     * @var SplObjectStorage
     */
    protected $visited;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->visited = new SplObjectStorage;
    }

    /**
     * Returns the number of visited nodes.
     *
     * @return integer
     */
    public function count()
    {
        return count( $this->visited );
    }

    /**
     * Visit the $visitable.
     *
     * Each node in the graph is visited once.
     *
     * @param ezcWorkflowVisitable $visitable
     * @return bool
     */
    public function visit( ezcWorkflowVisitable $visitable )
    {
        if ( $visitable instanceof ezcWorkflowNode )
        {
            if ( $this->visited->contains( $visitable ) )
            {
                return false;
            }

            $this->visited->attach( $visitable );
        }

        $this->doVisit( $visitable );

        return true;
    }

    /**
     * Perform the visit.
     *
     * @param ezcWorkflowVisitable $visitable
     */
    protected function doVisit( ezcWorkflowVisitable $visitable )
    {
    }
}
?>
