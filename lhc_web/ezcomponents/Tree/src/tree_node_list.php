<?php
/**
 * File containing the ezcTreeNodeList class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeNodeList represents a lists of nodes.
 *
 * The nodes in the list can be accessed through an array as this class
 * implements the ArrayAccess SPL interface. The array is indexed based on the
 * the node's ID.
 *
 * Example:
 * <code>
 * <?php
 *     // Create a list with two elements
 *     $list = new ezcTreeNodeList;
 *     $list->addNode( new ezcTreeNode( $tree, 'Leo' ) );
 *     $list->addNode( new ezcTreeNode( $tree, 'Virgo' ) );
 * 
 *     // Retrieve the list's size
 *     echo $list->size, "\n"; // prints 2
 * 
 *     // Find a node in the list
 *     $node = $list['Virgo'];
 * 
 *     // Add nodes in an alternative way
 *     $list['Libra'] = new ezcTreeNode( $tree, 'Libra' );
 * 
 *     // Remove a node from the list
 *     unset( $list['Leo'] );
 * 
 *     // Checking if a node exists
 *     if ( isset( $list['Scorpius'] ) )
 *     {
 *         // do something if it exists
 *     }
 * 
 *     // Use the associated data store to fetch the data for all nodes at once
 *     $list->fetchDataForNodes();
 * ?>
 * </code>
 *
 * @see ezcTreeNodeListIterator
 *
 * @property-read string $size
 *                The number of nodes in the list.
 * @property-read array(string=>ezcTreeNode) $nodes
 *                The nodes belonging to this list.
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeNodeList implements ArrayAccess
{
    /**
     * Holds the nodes of this list.
     *
     * @var array(ezcTreeNode)
     */
    private $nodes;

    /**
     * Constructs a new ezcTreeNodeList object.
     */
    public function __construct()
    {
        $this->nodes = array();
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'nodes':
                return $this->nodes;

            case 'size':
                return count( $this->nodes );

        }
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBasePropertyPermissionException if a read-only property is
     *         tried to be modified.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'nodes':
            case 'size':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns whether a node with the ID $nodeId exists in the list.
     *
     * This method is part of the SPL ArrayAccess interface.
     *
     * @param  string $nodeId
     * @return bool
     * @ignore
     */
    public function offsetExists( $nodeId )
    {
        return array_key_exists( $nodeId, $this->nodes );
    }

    /**
     * Returns the node with the ID $nodeId.
     *
     * This method is part of the SPL ArrayAccess interface.
     *
     * @param  string $nodeId
     * @return ezcTreeNode
     * @ignore
     */
    public function offsetGet( $nodeId )
    {
        return $this->nodes[$nodeId];
    }

    /**
     * Adds a new node with node ID $nodeId to the list.
     *
     * This method is part of the SPL ArrayAccess interface.
     * 
     * @throws ezcTreeInvalidClassException if the data to be set as array
     *         element is not an instance of ezcTreeNode
     * @throws ezcTreeIdsDoNotMatchException if the array index $nodeId does not
     *         match the tree node's ID that is stored in the $data object
     * @param  string      $nodeId
     * @param  ezcTreeNode $data
     * @ignore
     */
    public function offsetSet( $nodeId, $data )
    {
        if ( !$data instanceof ezcTreeNode )
        {
            throw new ezcTreeInvalidClassException( 'ezcTreeNode', get_class( $data ) );
        }
        if ( $data->id !== $nodeId )
        {
            throw new ezcTreeIdsDoNotMatchException( $data->id, $nodeId );
        }
        $this->addNode( $data );
    }

    /**
     * Removes the node with ID $nodeId from the list.
     *
     * This method is part of the SPL ArrayAccess interface.
     *
     * @param string $nodeId
     * @ignore
     */
    public function offsetUnset( $nodeId )
    {
        unset( $this->nodes[$nodeId] );
    }


    /**
     * Adds the node $node to the list.
     *
     * @param ezcTreeNode $node
     */
    public function addNode( ezcTreeNode $node )
    {
        $this->nodes[$node->id] = $node;
    }

    /**
     * Fetches data for all nodes in the node list.
     */
    public function fetchDataForNodes()
    {
        // We need to use a little trick to get to the tree object. We can do
        // that through ezcTreeNode objects that are part of this list. We
        // can't do that when the list is empty. In that case we just return.
        if ( count( $this->nodes ) === 0 )
        {
            return;
        }
        // Find a node in the list
        reset( $this->nodes );
        $node = current( $this->nodes );
        // Grab the tree and use it to fetch data for all nodes from the store
        $tree = $node->tree;
        $tree->store->fetchDataForNodes( $this );
    }
}
?>
