<?php
/**
 * File containing the ezcTreeNodeListIterator class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeNodeListIterator implements an iterator over an ezcTreeNodeList.
 *
 * The iterator is instantiated with both an implementation of an ezcTree and
 * an ezcTreeNodeList object. It can be used to iterate over all the nodes
 * in a list.
 *
 * Example:
 * <code>
 * <?php
 *     // fetch all the nodes in a subtree as an ezcNodeList
 *     $nodeList = $tree->fetchSubtree( 'pan' );
 *     foreach ( new ezcTreeNodeListIterator( $tree, $nodeList ) as $nodeId => $data )
 *     {
 *         // do something with the node ID and data - data is fetched on
 *         // demand
 *     }
 * ?>
 * </code>
 *
 * Data for the nodes in the node lists is fetched on demand, unless
 * the "prefetch" argument is set to true. In that case the iterator will
 * fetch the data when the iterator is instantiated. This reduces the number
 * of queries made for database and persistent object based data stores, but
 * increases memory usage.
 *
 * Example:
 * <code>
 * <?php
 *     // fetch all the nodes in a subtree as an ezcNodeList
 *     $nodeList = $tree->fetchSubtree( 'Uranus' );
 *     // instantiate an iterator with pre-fetching enabled
 *     foreach ( new ezcTreeNodeListIterator( $tree, $nodeList, true ) as $nodeId => $data )
 *     {
 *         // do something with the node ID and data - data is fetched when
 *         // the iterator is instatiated.
 *     }
 * ?>
 * </code>
 *
 * @package Tree
 * @version 1.1.4
 * @mainclass
 */
class ezcTreeNodeListIterator implements Iterator
{
    /**
     * Holds the nodes of this list.
     *
     * @var array(ezcTreeNode)
     */
    private $nodeList;

    /**
     * Holds a link to the tree that contains the nodes that are iterated over.
     *
     * This is used for accessing the data store so that data can be fetched
     * on-demand.
     *
     * @var ezcTree
     */
    private $tree;

    /**
     * Constructs a new ezcTreeNodeListIterator object over $nodeList.
     *
     * The $tree argument is used so that data can be fetched on-demand.
     *
     * @param ezcTree         $tree
     * @param ezcTreeNodeList $nodeList
     * @param bool            $prefetch
     */
    public function __construct( ezcTree $tree, ezcTreeNodeList $nodeList, $prefetch = false )
    {
        $this->tree = $tree;
        if ( $prefetch )
        {
            $this->tree->store->fetchDataForNodes( $nodeList );
        }
        $this->nodeList = $nodeList->nodes;
    }

    /**
     * Rewinds the internal pointer back to the start of the nodelist.
     */
    public function rewind()
    {
        reset( $this->nodeList );
        if ( count( $this->nodeList ) )
        {
            $this->valid = true;
        }
        else 
        {
            $this->valid = false;
        }
    }

    /**
     * Returns the node ID of the current node.
     *
     * @return string
     */
    public function key()
    {
        return key( $this->nodeList );
    }

    /**
     * Returns the data belonging to the current node, and fetches the data in
     * case on-demand fetching is required.
     *
     * @return mixed
     */
    public function current()
    {
        $node = current( $this->nodeList );
        return $node->data;
    }

    /**
     * Advances the internal pointer to the next node in the nodelist.
     */
    public function next()
    {
        $nextElem = next( $this->nodeList );
        if ( $nextElem === false )
        {
            $this->valid = false;
        }
    }

    /**
     * Returns whether the internal pointer is still valid.
     *
     * It returns false when the end of list has been reached.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->valid;
    }
}
?>
