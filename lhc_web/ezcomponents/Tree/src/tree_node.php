<?php
/**
 * File containing the ezcTreeNode class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeNode represents a node in a tree.
 *
 * The methods that operate on nodes (fetchChildren, fetchPath, ...,
 * isSiblingOf) are all marshalled to calls on the tree (that is stored in the
 * $tree private variable) itself. 
 *
 * Example:
 * <code>
 * <?php
 *     // Creates a new node with ID 'O' and as data 'Oxygen'
 *     $node = new ezcTreeNode( $this->tree, 'O', 'Oxygen' );
 * 
 *     // Adds a node as child element to another already create node in a tree
 *     $parentNode->addChild( $node );
 * ?>
 * </code>
 *
 * To use your own implementation of tree nodes, you can override the class
 * that is used by the tree by setting the nodeClassName property of the
 * ezcTree class. The class must inherit from this class though.
 *
 * @property-read string  $id          The ID that uniquely identifies a node
 * @property-read ezcTree $tree        The tree object that this node belongs to
 * @property      mixed   $data        The data belonging to a node
 * @property      bool    $dataFetched Whether the data for this node has been
 *                                     fetched. Should *only* be modified by
 *                                     data store implementations.
 * @property      bool    $dataStored  Whether the data for this node has been
 *                                     stored. Should *only* be modified by
 *                                     data store implementations.
 *
 * @package Tree
 * @version 1.1.4
 * @mainclass
 */
class ezcTreeNode implements ezcTreeVisitable
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Constructs a new ezcTreeNode object with ID $nodeId on tree $tree.
     *
     * If a third argument is specified it is used as data for the new node.
     *
     * @param ezcTree $tree
     * @param string  $nodeId
     * @param mixed   $data
     */
    public function __construct( ezcTree $tree, $nodeId )
    {
        $this->properties['id'] = (string) $nodeId;
        $this->properties['tree'] = $tree;

        if ( func_num_args() === 2 )
        {
            $this->properties['data'] = null;
            $this->properties['dataFetched'] = false;
            $this->properties['dataStored'] = true;
        }
        else
        {
            $this->properties['data'] = func_get_arg( 2 );
            $this->properties['dataFetched'] = true;
            $this->properties['dataStored'] = false;
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'data':
                if ( $this->properties['dataFetched'] === false )
                {
                    // fetch the data on the fly
                    $this->tree->store->fetchDataForNode( $this );
                }
                // break intentionally missing
            case 'id':
            case 'dataFetched':
            case 'dataStored':
            case 'tree':
                return $this->properties[$name];

        }
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBasePropertyPermissionException if a read-only property is
     *         tried to be modified.
     * @throws ezcBaseValueException if trying to assign a wrong value to
     *         the property
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'id':
            case 'tree':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            case 'data':
                if ( !$this->properties['dataFetched'] )
                {
                    $this->tree->store->fetchDataForNode( $this );
                    $this->properties['dataFetched'] = true;
                }

                $this->properties[$name] = $value;
                $this->properties['dataStored'] = false;
                $this->tree->store->storeDataForNode( $this );
                return;

            case 'dataFetched':
            case 'dataStored':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'boolean' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name     
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'id':
            case 'tree':
            case 'data':
            case 'dataFetched':
            case 'dataStored':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Inject data.
     *
     * Used to set the data from a data loader. Should not be used for 
     * interfacing with the tree node, since the node will not be flagged as 
     * modified by this method.
     * 
     * @access private
     * @param string $data 
     * @return void
     */
    public function injectData( $data )
    {
        $this->properties['data'] = $data;
    }

    /**
     * Implements the accept method for visiting.
     *
     * @param ezcTreeVisitor $visitor
     */
    public function accept( ezcTreeVisitor $visitor )
    {
        $visitor->visit( $this );
        foreach ( $this->fetchChildren()->nodes as $childNode )
        {
            $childNode->accept( $visitor );
        }
    }

    /**
     * Adds the node $node as child of the current node to the tree.
     *
     * @param ezcTreeNode $node
     */
    public function addChild( ezcTreeNode $node )
    {
        $this->tree->addChild( $this->id, $node );
    }

    /**
     * Returns all the children of this node.
     *
     * @return ezcTreeNodeList
     */
    public function fetchChildren()
    {
        return $this->tree->fetchChildren( $this->id );
    }

    /**
     * Returns all the nodes in the path from the root node to this node.
     *
     * @return ezcTreeNodeList
     */
    public function fetchPath()
    {
        return $this->tree->fetchPath( $this->id );
    }

    /**
     * Returns the parent node of this node.
     *
     * @return ezcTreeNode
     */
    public function fetchParent()
    {
        return $this->tree->fetchParent( $this->id );
    }

    /**
     * Returns this node and all its children, sorted according to the
     * {@link http://en.wikipedia.org/wiki/Depth-first_search Depth-first sorting}
     * algorithm.
     *
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeDepthFirst()
    {
        return $this->tree->fetchSubtreeDepthFirst( $this->id );
    }

    /**
	 * Alias for fetchSubtreeDepthFirst().
     *
     * @see fetchSubtreeDepthFirst
     * @return ezcTreeNodeList
     */
    public function fetchSubtree()
    {
        return $this->fetchSubtreeDepthFirst();
    }

    /**
     * Returns this node and all its children, sorted accoring to the
     * {@link http://en.wikipedia.org/wiki/Breadth-first_search Breadth-first sorting}
     * algorithm.
     *
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeBreadthFirst()
    {
        return $this->tree->fetchSubtreeBreadthFirst( $this->id );
    }

    /**
     * Returns the number of direct children of this node.
     *
     * @return int
     */
    public function getChildCount()
    {
        return $this->tree->getChildCount( $this->id );
    }

    /**
     * Returns the number of children of this node, recursively iterating over
     * the children.
     *
     * @return int
     */
    public function getChildCountRecursive()
    {
        return $this->tree->getChildCountRecursive( $this->id );
    }

    /**
     * Returns the distance from the root node to this node.
     *
     * @return int
     */
    public function getPathLength()
    {
        return $this->tree->getPathlength( $this->id );
    }

    /**
     * Returns whether this node has children.
     *
     * @return bool
     */
    public function hasChildNodes()
    {
        return $this->tree->hasChildNodes( $this->id );
    }

    /**
     * Returns whether this node is a direct child of the $parentNode node.
     *
     * @param ezcTreeNode $parentNode
     *
     * @return bool
     */
    public function isChildOf( ezcTreeNode $parentNode )
    {
        return $this->tree->isChildOf( $this->id, $parentNode->id );
    }

    /**
     * Returns whether this node is a direct or indirect child of the
     * $parentNode node.
     *
     * @param ezcTreeNode $parentNode
     *
     * @return bool
     */
    public function isDescendantOf( ezcTreeNode $parentNode )
    {
        return $this->tree->isDescendantOf( $this->id, $parentNode->id );
    }

    /**
     * Returns whether this node, and the $child2Node node are are siblings
     * (ie, they share the same parent).
     *
     * @param ezcTreeNode $child2Node
     *
     * @return bool
     */
    public function isSiblingOf( ezcTreeNode $child2Node )
    {
        return $this->tree->isSiblingOf( $this->id, $child2Node->id );
    }

    /**
     * Returns the text representation of a node (its ID).
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->id;
    }
}
?>
