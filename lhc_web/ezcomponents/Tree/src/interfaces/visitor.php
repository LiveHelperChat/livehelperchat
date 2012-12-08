<?php
/**
 * File containing the ezcTreeVisitor interface.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * Interface for visitor implementations that want to process
 * a tree using the Visitor design pattern.
 *
 * visit() is called on each of the nodes in the tree in a top-down,
 * depth-first fashion.
 *
 * Start the processing of the tree by calling accept() on the tree
 * passing the visitor object as the sole parameter.
 *
 * @package Tree
 * @version 1.1.4
 */
interface ezcTreeVisitor
{
    /**
     * Visit the $visitable.
     *
     * Each node in the graph is visited once.
     *
     * @param ezcTreeVisitable $visitable
     * @return bool
     */
    public function visit( ezcTreeVisitable $visitable );
}
?>
