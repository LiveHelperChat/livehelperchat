<?php
/**
 * File containing the ezcTreeVisitorGraphViz class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * An implementation of the ezcTreeVisitor interface that generates
 * GraphViz/dot markup for a tree structure.
 *
 * <code>
 * <?php
 *     $visitor = new ezcTreeVisitorGraphViz;
 *     $tree->accept( $visitor );
 *     echo (string) $visitor; // print the plot
 * ?>
 * </code>
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeVisitorGraphViz implements ezcTreeVisitor
{
    /**
     * Holds the displayed strings for each of the nodes.
     *
     * @var array(string=>string)
     */
    protected $nodes = array();

    /**
     * Holds all the edges of the graph.
     *
     * @var array(id=>array(ezcTreeNode))
     */
    protected $edges = array();

    /**
     * Creates a graphviz compliant ID out of the ID identifying $node.
     *
     * @param ezcTreeNode $node
     * @return string
     */
    private function createId( ezcTreeNode $node )
    {
        return preg_replace( '/[^A-Za-z0-9_]/', '', $node->id ) . '_'. base_convert( sprintf( '%u', crc32( $node->id ) ), 16, 36 );
    }

    /**
     * Visits the node and sets the the member variables according to the node
     * type and contents.
     *
     * @param ezcTreeVisitable $visitable
     * @return bool
     */
    public function visit( ezcTreeVisitable $visitable )
    {
        if ( $visitable instanceof ezcTreeNode )
        {
            $id = $this->createId( $visitable );
            $this->nodes[$id] = $visitable->id;

            $parent = $visitable->fetchParent();
            if ( $parent )
            {
                $parentId = $this->createId( $parent );
                $this->edges[$parentId][] = $id;
            }
        }

        return true;
    }

    /**
     * Returns the contents as a graphviz .dot file structure.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $dot = "digraph Tree {\n";

        foreach ( $this->nodes as $key => $value )
        {
            $dot .= sprintf(
              "node%s [label=\"%s\"]\n",
              $key,
              $value
            );
        }

        $dot .= "\n";

        foreach ( $this->edges as $fromNode => $toNodes )
        {
            foreach ( $toNodes as $toNode )
            {
                $dot .= sprintf(
                  "node%s -> node%s\n",

                  $fromNode,
                  $toNode
                );
            }
        }

        return $dot . "}\n";
    }
}
?>
