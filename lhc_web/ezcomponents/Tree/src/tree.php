<?php
/**
 * File containing the ezcTree class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTree is an abstract class from which all the tree implementations
 * inherit.
 *
 * Example:
 * <code>
 * <?php
 *     // Instantiating an existing tree, and creating a new tree is done through
 *     // the inherited classes
 *     
 *     // Creating a new in-memory tree
 *     $tree = ezcTreeMemory::create( new ezcTreeMemoryDataStore() );
 * 
 *     // Opening an existing tree in an XML file
 *     $tree = new ezcTreeXml( 'test.xml', new ezcTreeXmlInternalDataStore() );
 * 
 *     // Opening an existing tree from a database, using a nested set backend
 *     // - This retrieves data from the ezcTreeDbExternalTableDataStore store
 *     //   using $this->dbh as database handle, $dataTable as table where to fetch
 *     //   data from using 'id' as ID field.
 *     $store = new ezcTreeDbExternalTableDataStore( $this->dbh, $dataTable, 'id' );
 *     // - It uses the 'nested_set' table for keeping the tree structure
 *     $tree = new ezcTreeDbNestedSet( $this->dbh, 'nested_set', $store );
 * 
 *     // Fetching nodes and subtrees
 *     $node = $tree->fetchNodeById( 'He' );
 *     $nodeList = $tree->fetchSubtree( 'Pantherinae' );
 * 
 *     // Checking for relations between nodes
 *     $tree->isDescendantOf( 'Tiger', 'Panthera' );
 *     $tree->isSiblingOf( 'Lion', 'Leopard' );
 * ?>
 * </code>
 *
 * @property-read ezcTreeXmlDataStore $store
 *                The data store that is used for retrieving/storing data.
 * @property      string              $nodeClassName
 *                Which class is used as tree node - this class *must* inherit
 *                the ezcTreeNode class.
 * @property      boolean             $autoId
 *                When set to true, you can add nodes to the database without
 *                setting the ID. This only works with numeric keys however.
 * 
 * @package Tree
 * @version 1.1.4
 */
abstract class ezcTree implements ezcTreeVisitable
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array( 'nodeClassName' => 'ezcTreeNode' );

    /**
     * Contains whether a transaction has been started.
     *
     * @var bool
     */
    protected $inTransaction = false;

    /**
     * Contains whether we are in a transaction commit stage.
     *
     * @var bool
     */
    protected $inTransactionCommit = false;

    /**
     * Contains a list of transaction items.
     *
     * @var array(ezcTreeTransactionItem)
     */
    private $transactionList = array();

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
            case 'autoId':
            case 'store':
            case 'nodeClassName':
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
     * @throws ezcBaseValueException if trying to assign a wrong value to the
     *         property
     * @throws ezcBaseInvalidParentClassException
     *         if the class name passed as replacement for the ezcTreeNode
     *         classs does not inherit from the ezcTreeNode class.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'store':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            case 'autoId':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'boolean' );
                }
                $this->properties[$name] = $value;
                break;

            case 'nodeClassName':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string that contains a class name' );
                }

                // Check if the passed classname actually implements the
                // correct parent class.
                if ( 'ezcTreeNode' !== $value &&
                    !in_array( 'ezcTreeNode', class_parents( $value ) ) )
                {
                    throw new ezcBaseInvalidParentClassException( 'ezcTreeNode', $value );
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
            case 'autoId':
            case 'store':
            case 'nodeClassName':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * This method checks whether a node ID is valid to be used in a backend.
     *
     * @throws ezcTreeInvalidNodeIDException if the node is not valid.
     *
     * @param string $nodeId
     */
    protected function checkNodeId( $nodeId )
    {
        /* The default implementation does not check anything */
    }

    /**
     * This method generates the next node ID.
     *
     * @return integer
     */
    abstract protected function generateNodeID();

    /**
     * Creates a new tree node with node ID $nodeId and $data.
     *
     * This method returns by default an object of the ezcTreeNode class, 
     * however if a replacement is configured through the nodeClassName property
     * an object of that class is returned instead. This object is guaranteed
     * to inherit from ezcTreeNode.
     *
     * @param string $nodeId
     * @param mixed  $data
     * @return ezcTreeNode
     */
    public function createNode( $nodeId, $data )
    {
        if ( $nodeId === null )
        {
            if ( $this->properties['autoId'] )
            {
                $nodeId = $this->generateNodeID();
            }
            else
            {
                throw new ezcTreeInvalidIdException( null, '' );
            }
        }
        $this->checkNodeID( $nodeId );
        $className = $this->properties['nodeClassName'];
        return new $className( $this, $nodeId, $data );
    }

    /**
     * Implements the accept method for visiting.
     *
     * @param ezcTreeVisitor $visitor
     */
    public function accept( ezcTreeVisitor $visitor )
    {
        $visitor->visit( $this );
        $root = $this->getRootNode();
        if ( $root instanceof ezcTreeNode )
        {
            $root->accept( $visitor );
        }
    }

    /**
     * Returns whether the node with ID $nodeId exists.
     *
     * @param string $nodeId
     * @return bool
     */
    abstract public function nodeExists( $nodeId );

    /**
     * Returns the node identified by the ID $nodeId.
     *
     * @param string $nodeId
     * @throws ezcTreeUnknownIdException if there is no node with ID $nodeId
     * @return ezcTreeNode
     */
    public function fetchNodeById( $nodeId )
    {
        if ( !$this->nodeExists( $nodeId ) )
        {
            throw new ezcTreeUnknownIdException( $nodeId );
        }
        $className = $this->properties['nodeClassName'];
        $node = new $className( $this, $nodeId );

        return $node;
    }

    /**
     * Returns all the children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    abstract public function fetchChildren( $nodeId );

    /**
     * Returns the parent node of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return ezcTreeNode
     */
    abstract public function fetchParent( $nodeId );

    /**
     * Returns all the nodes in the path from the root node to the node with ID
     * $nodeId, including those two nodes.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    abstract public function fetchPath( $nodeId );

    /**
     * Alias for fetchSubtreeDepthFirst().
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    abstract public function fetchSubtree( $nodeId );

    /**
     * Returns the node with ID $nodeId and all its children, sorted according to
     * the {@link http://en.wikipedia.org/wiki/Breadth-first_search Breadth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    abstract public function fetchSubtreeBreadthFirst( $nodeId );

    /**
     * Returns the node with ID $nodeId and all its children, sorted according to
     * the {@link http://en.wikipedia.org/wiki/Depth-first_search Depth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    abstract public function fetchSubtreeDepthFirst( $nodeId );

    /**
     * Returns the number of direct children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    abstract public function getChildCount( $nodeId );

    /**
     * Returns the number of children of the node with ID $nodeId, recursively.
     *
     * @param string $nodeId
     * @return int
     */
    abstract public function getChildCountRecursive( $nodeId );

    /**
     * Returns the distance from the root node to the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    abstract public function getPathLength( $nodeId );

    /**
     * Returns whether the node with ID $nodeId has children.
     *
     * @param string $nodeId
     * @return bool
     */
    abstract public function hasChildNodes( $nodeId );

    /**
     * Returns whether the node with ID $childId is a direct child of the node
     * with ID $parentId.
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    abstract public function isChildOf( $childId, $parentId );

    /**
     * Returns whether the node with ID $childId is a direct or indirect child
     * of the node with ID $parentId.
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    abstract public function isDescendantOf( $childId, $parentId );

    /**
     * Returns whether the nodes with IDs $child1Id and $child2Id are siblings
     * (ie, they share the same parent).
     *
     * @param string $child1Id
     * @param string $child2Id
     * @return bool
     */
    abstract public function isSiblingOf( $child1Id, $child2Id );

    /**
     * Sets a new node as root node, this also wipes out the whole tree.
     *
     * @param ezcTreeNode $node
     */
    abstract public function setRootNode( ezcTreeNode $node );

    /**
     * Returns the root node.
     *
     * @return ezcTreeNode
     */
    abstract public function getRootNode();

    /**
     * Adds the node $childNode as child of the node with ID $parentId.
     *
     * @param string $parentId
     * @param ezcTreeNode $childNode
     */
    abstract public function addChild( $parentId, ezcTreeNode $childNode );

    /**
     * Deletes the node with ID $nodeId from the tree, including all its children.
     *
     * @param string $nodeId
     */
    abstract public function delete( $nodeId );

    /**
     * Moves the node with ID $nodeId as child to the node with ID $targetParentId.
     *
     * @param string $nodeId
     * @param string $targetParentId
     */
    abstract public function move( $nodeId, $targetParentId );

    /**
     * Copies all the children of node $fromNode to node $toNode recursively.
     *
     * This method copies all children recursively from $fromNode to $toNode.
     * The $fromNode belongs to the $from tree and the $toNode to the $to tree.
     * Data associated with the nodes is copied as well from the store
     * associated with the $from tree to the $to tree.
     *
     * @param ezcTree $from
     * @param ezcTree $to
     * @param ezcTreeNode $fromNode
     * @param ezcTreeNode $toNode
     */
    private static function copyChildren( ezcTree $from, ezcTree $to, ezcTreeNode $fromNode, ezcTreeNode $toNode )
    {
        $children = $fromNode->fetchChildren();
        foreach ( new ezcTreeNodeListIterator( $from, $children, true ) as $childNodeKey => $childNodeData )
        {
            $fromChildNode = $from->fetchNodeById( $childNodeKey );
            $toChildNode = new ezcTreeNode( $to, $childNodeKey, $childNodeData );
            $toNode->addChild( $toChildNode );
            self::copyChildren( $from, $to, $fromChildNode, $toChildNode );
        }
    }

    /**
     * Copies the tree in $from to the empty tree in $to.
     *
     * This method copies all the nodes, including associated data from the
     * used data store, from the tree $from to the tree $to.  Because this
     * function uses internally setRootNode() the target tree will be cleared
     * out automatically. The method will not check whether the $from and $to
     * trees share the same database table or data store, so make sure they are
     * different to prevent unexpected behavior.
     *
     * @param ezcTree $from
     * @param ezcTree $to
     */
    public static function copy( ezcTree $from, ezcTree $to )
    {
        $fromRootNode = $from->getRootNode();
        $to->setRootNode( new ezcTreeNode( $to, $fromRootNode->id, $fromRootNode->data ) );
        $toRootNode = $to->getRootNode();
        self::copyChildren( $from, $to, $fromRootNode, $toRootNode );
    }

    /**
     * Returns whether we are currently in a transaction or not
     *
     * @return bool
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * Returns whether we are currently in a transaction commit state or not
     *
     * @return bool
     */
    public function inTransactionCommit()
    {
        return $this->inTransactionCommit;
    }

    /**
     * Starts an transaction in which all tree modifications are queued until 
     * the transaction is committed with the commit() method.
     */
    public function beginTransaction()
    {
        if ( $this->inTransaction )
        {
            throw new ezcTreeTransactionAlreadyStartedException;
        }
        $this->inTransaction = true;
        $this->transactionList = array();
    }

    /**
     * Commits the transaction by running the stored instructions to modify
     * the tree structure.
     */
    public function commit()
    {
        if ( !$this->inTransaction )
        {
            throw new ezcTreeTransactionNotStartedException;
        }
        $this->inTransaction = false;
        $this->inTransactionCommit = true;
        foreach ( $this->transactionList as $transactionItem )
        {
            switch ( $transactionItem->type )
            {
                case ezcTreeTransactionItem::ADD:
                    $this->addChild( $transactionItem->parentId, $transactionItem->node );
                    break;

                case ezcTreeTransactionItem::DELETE:
                    $this->delete( $transactionItem->nodeId );
                    break;

                case ezcTreeTransactionItem::MOVE:
                    $this->move( $transactionItem->nodeId, $transactionItem->parentId );
                    break;
            }
        }
        $this->inTransactionCommit = false;
        $this->fixateTransaction();
    }

    /**
     * Adds a new transaction item to the list.
     *
     * @param ezcTreeTransactionItem $item
     */
    protected function addTransactionItem( ezcTreeTransactionItem $item )
    {
        $this->transactionList[] = $item;
    }

    /**
     * Rolls back the transaction by clearing all stored instructions.
     */
    public function rollback()
    {
        if ( !$this->inTransaction )
        {
            throw new ezcTreeTransactionNotStartedException;
        }
        $this->inTransaction = false;
        $this->transactionList = array();
    }
}
?>
