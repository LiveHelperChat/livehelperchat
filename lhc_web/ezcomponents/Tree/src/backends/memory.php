<?php
/**
 * File containing the ezcTreeMemory class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeMemory is an implementation of a tree backend that operates on
 * an in-memory tree structure. Meta-information is kept in objects of the
 * ezcTreeMemoryNode class.
 *
 * Example:
 * <code>
 * <?php
 *     // Create a new tree
 *     $tree = ezcTreeMemory::create( new ezcTreeMemoryDataStore() );
 *     // or
 *     $tree = new ezcTreeMemory( new ezcTreeMemoryDataStore() );
 * ?>
 * </code>
 *
 * See {@link ezcTree} for examples on how to operate on the tree.
 *
 * @property-read ezcTreeXmlDataStore $store
 *                The data store that is used for retrieving/storing data.
 * @property      string              $nodeClassName
 *                Which class is used as tree node - this class *must* inherit
 *                the ezcTreeNode class.
 *
 * @package Tree
 * @version 1.1.4
 * @mainclass
 */
class ezcTreeMemory extends ezcTree
{
    /**
     * Contains a list of all nodes, indexed by node ID that link directly to the create node so that they can be looked up quickly.
     *
     * @var array(string=>ezcTreeMemoryNode)
     */
    private $nodeList = array();

    /**
     * Contains the root node.
     *
     * @var ezcTreeMemoryNode
     */
    private $rootNode;

    /**
     * Stores the last auto generated ID that was used.
     *
     * @var integer $autoNodeId
     */
    private $autoNodeId = 0;

    /**
     * Constructs a new ezcTreeMemory object.
     *
     * The store that is used for data storage should be passed as the
     * $store argument.
     *
     * @param ezcTreeMemoryDataStore $store
     */
    protected function __construct( ezcTreeMemoryDataStore $store )
    {
        $this->properties['store'] = $store;
        $this->properties['autoId'] = false;
    }

    /**
     * This method generates the next node ID.
     *
     * @return integer
     */
    protected function generateNodeID()
    {
        $this->autoNodeId++;
        return $this->autoNodeId;
    }

    /**
     * A factory method that creates a new empty tree using the data store $store.
     *
     * @param ezcTreeMemoryDataStore $store
     * @return ezcTreeMemory
     */
    public static function create( ezcTreeMemoryDataStore $store )
    {
        $newTree = new ezcTreeMemory( $store );
        $newTree->nodeList = null;
        $newTree->rootNode = null;
        return $newTree;
    }

    /**
     * Returns whether the node with ID $nodeId exists.
     *
     * @param string $nodeId
     * @return bool
     */
    public function nodeExists( $nodeId )
    {
        return isset( $this->nodeList[$nodeId] );
    }

    /**
     * Returns the node identified by the ID $nodeId.
     *
     * @param string $nodeId
     * @throws ezcTreeInvalidIdException if there is no node with ID $nodeId
     * @return ezcTreeNode
     */
    public function fetchNodeById( $nodeId )
    {
        return $this->getNodeById( $nodeId )->node;
    }

    /**
     * Returns the node container for node $nodeId.
     *
     * @param string $nodeId
     * @throws ezcTreeInvalidIdException if there is no node with ID $nodeId
     * @return ezcTreeMemoryNode
     */
    private function getNodeById( $nodeId )
    {
        if ( !$this->nodeExists( $nodeId ) )
        {
            throw new ezcTreeUnknownIdException( $nodeId );
        }
        return $this->nodeList[$nodeId];
    }

    /**
     * Returns all the children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchChildren( $nodeId )
    {
        $treeNode = $this->getNodeById( $nodeId );
        $list = new ezcTreeNodeList;
        foreach ( $treeNode->children as $nodeId => $child )
        {
            $list->addNode( $child->node );
        }
        return $list;
    }

    /**
     * Returns the parent node of the node with ID $nodeId.
     *
     * This method returns null if there is no parent node.
     *
     * @param string $nodeId
     * @return ezcTreeNode
     */
    public function fetchParent( $nodeId )
    {
        $treeNode = $this->getNodeById( $nodeId );
        $parentNode = $treeNode->parent;
        return $parentNode !== null ? $parentNode->node : null;
    }

    /**
     * Returns all the nodes in the path from the root node to the node with ID
     * $nodeId, including those two nodes.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchPath( $nodeId )
    {
        $list = new ezcTreeNodeList;
        $memoryNode = $this->getNodeById( $nodeId );

        $nodes = array();
        $nodes[] = $memoryNode->node;

        $memoryNode = $memoryNode->parent;

        while ( $memoryNode !== null )
        {
            $nodes[] =  $memoryNode->node;
            $memoryNode = $memoryNode->parent;
        }

        $list = new ezcTreeNodeList;
        foreach ( array_reverse( $nodes ) as $node )
        {
            $list->addNode( $node );
        }
        return $list;
    }

    /**
     * Adds the children nodes of the node $memoryNode to the
     * ezcTreeNodeList $list.
     *
     * @param ezcTreeNodeList $list
     * @param ezcTreeMemoryNode $memoryNode
     */
    private function addChildNodesDepthFirst( ezcTreeNodeList $list, ezcTreeMemoryNode $memoryNode )
    {
        foreach ( $memoryNode->children as $nodeId => $childMemoryNode )
        {
            $list->addNode( $childMemoryNode->node );
            $this->addChildNodesDepthFirst( $list, $childMemoryNode );
        }
    }

    /**
     * Returns the node with ID $nodeId and all its children, sorted accoring to
     * the {@link http://en.wikipedia.org/wiki/Depth-first_search Depthth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeDepthFirst( $nodeId )
    {
        $list = new ezcTreeNodeList;
        $memoryNode = $this->getNodeById( $nodeId );
        $list->addNode( $memoryNode->node );
        $this->addChildNodesDepthFirst( $list, $memoryNode );
        return $list;
    }

    /**
     * Alias for fetchSubtreeDepthFirst().
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtree( $nodeId )
    {
        return $this->fetchSubtreeDepthFirst( $nodeId );
    }

    /**
     * Adds the children nodes of the node $memoryNode to the
     * ezcTreeNodeList $list.
     *
     * @param ezcTreeNodeList $list
     * @param ezcTreeMemoryNode $memoryNode
     */
    private function addChildNodesBreadthFirst( ezcTreeNodeList $list, ezcTreeMemoryNode $memoryNode )
    {
        foreach ( $memoryNode->children as $nodeId => $childMemoryNode )
        {
            $list->addNode( $childMemoryNode->node );
        }
        foreach ( $memoryNode->children as $nodeId => $childMemoryNode )
        {
            $this->addChildNodesBreadthFirst( $list, $childMemoryNode );
        }
    }

    /**
     * Returns the node with ID $nodeId and all its children, sorted according to
     * the {@link http://en.wikipedia.org/wiki/Breadth-first_search Breadth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeBreadthFirst( $nodeId )
    {
        $list = new ezcTreeNodeList;
        $memoryNode = $this->getNodeById( $nodeId );
        $list->addNode( $memoryNode->node );
        $this->addChildNodesBreadthFirst( $list, $memoryNode );
        return $list;
    }

    /**
     * Returns the number of direct children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    public function getChildCount( $nodeId )
    {
        return count( $this->getNodeById( $nodeId )->children );
    }

    /**
     * Helper method that iterates recursively over the children of $node to
     * count the number of children.
     *
     * @param integer $count
     * @param ezcTreeMemoryNode $node
     */
    private function countChildNodes( &$count, ezcTreeMemoryNode $node )
    {
        foreach ( $node->children as $nodeId => $node )
        {
            $count++;
            $this->countChildNodes( $count, $node );
        }
    }

    /**
     * Returns the number of children of the node with ID $nodeId, recursively
     *
     * @param string $nodeId
     * @return int
     */
    public function getChildCountRecursive( $nodeId )
    {
        $count = 0;
        $node = $this->getNodeById( $nodeId );
        $this->countChildNodes( $count, $node );
        return $count;
    }

    /**
     * Returns the distance from the root node to the node with ID $nodeId
     *
     * @param string $nodeId
     * @return int
     */
    public function getPathLength( $nodeId )
    {
        $childNode = $this->getNodeById( $nodeId );
        $length = -1;

        while ( $childNode !== null )
        {
            $childNode = $childNode->parent;
            $length++;
        }
        return $length;
    }

    /**
     * Returns whether the node with ID $nodeId has children
     *
     * @param string $nodeId
     * @return bool
     */
    public function hasChildNodes( $nodeId )
    {
        return count( $this->getNodeById( $nodeId )->children ) > 0;
    }

    /**
     * Returns whether the node with ID $childId is a direct child of the node
     * with ID $parentId
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    public function isChildOf( $childId, $parentId )
    {
        $childNode = $this->getNodeById( $childId );
        $parentNode = $this->getNodeById( $parentId );

        if ( $childNode->parent->node === $parentNode->node )
        {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the node with ID $childId is a direct or indirect child
     * of the node with ID $parentId
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    public function isDescendantOf( $childId, $parentId )
    {
        $childNode = $this->getNodeById( $childId );
        $parentNode = $this->getNodeById( $parentId );

        if ( $childNode === $parentNode )
        {
            return false;
        }

        while ( $childNode !== null )
        {
            if ( $childNode->node === $parentNode->node )
            {
                    return true;
            }
            $childNode = $childNode->parent;
        }
        return false;
    }

    /**
     * Returns whether the nodes with IDs $child1Id and $child2Id are siblings
     * (ie, the share the same parent)
     *
     * @param string $child1Id
     * @param string $child2Id
     * @return bool
     */
    public function isSiblingOf( $child1Id, $child2Id )
    {
        $elem1 = $this->getNodeById( $child1Id );
        $elem2 = $this->getNodeById( $child2Id );
        return (
            ( $child1Id !== $child2Id ) && 
            ( $elem1->parent === $elem2->parent )
        );
    }

    /**
     * Sets a new node as root node, this wipes also out the whole tree
     *
     * @param ezcTreeNode $node
     */
    public function setRootNode( ezcTreeNode $node )
    {
        // wipe nodelist and data
        $this->nodeList = array();
        $this->store->deleteDataForAllNodes();

        // replace root node
        $newObj = new ezcTreeMemoryNode( $node, array() );
        $this->rootNode = $newObj;

        // Add to node list
        $this->nodeList[$node->id] = $newObj;
    }

    /**
     * Returns the root node
     *
     * This methods returns null if there is no root node.
     *
     * @return ezcTreeNode
     */
    public function getRootNode()
    {
        if ( $this->rootNode )
        {
            return $this->rootNode->node;
        }
        return null;
    }

    /**
     * Adds the node $childNode as child of the node with ID $parentId
     *
     * @param string $parentId
     * @param ezcTreeNode $childNode
     */
    public function addChild( $parentId, ezcTreeNode $childNode )
    {
        if ( $this->inTransaction )
        {
            $this->addTransactionItem( new ezcTreeTransactionItem( ezcTreeTransactionItem::ADD, $childNode, null, $parentId ) );
            return;
        }

        // Locate parent node
        $parentMemoryNode = $this->getNodeById( $parentId );

        // Create new node
        $newObj = new ezcTreeMemoryNode( $childNode, array(), $parentMemoryNode );

        // Append to parent node
        $parentMemoryNode->children[$childNode->id] = $newObj;

        // Add to node list
        $this->nodeList[$childNode->id] = $newObj;
    }

    /**
     * Deletes the node with ID $nodeId from the tree, including all its children
     *
     * @param string $nodeId
     */
    public function delete( $nodeId )
    {
        if ( $this->inTransaction )
        {
            $this->addTransactionItem( new ezcTreeTransactionItem( ezcTreeTransactionItem::DELETE, null, $nodeId ) );
            return;
        }

        // locate node to move
        $nodeToDelete = $this->getNodeById( $nodeId );

        // fetch the whole subtree and delete all the associated data
        $children = $nodeToDelete->node->fetchSubtree();
        $this->store->deleteDataForNodes( $children );

        // Use the parent to remove the child
        unset( $nodeToDelete->parent->children[$nodeId] );

        // Remove the node and all its children
        foreach ( new ezcTreeNodeListIterator( $this, $children ) as $nodeId => $data )
        {
            unset( $this->nodeList[$nodeId] );
        }
    }

    /**
     * Moves the node with ID $nodeId as child to the node with ID $targetParentId
     *
     * @param string $nodeId
     * @param string $targetParentId
     */
    public function move( $nodeId, $targetParentId )
    {
        if ( $this->inTransaction )
        {
            $this->addTransactionItem( new ezcTreeTransactionItem( ezcTreeTransactionItem::MOVE, null, $nodeId, $targetParentId ) );
            return;
        }

        // locate node to move
        $nodeToMove = $this->getNodeById( $nodeId );

        // locate new parent
        $newParent = $this->getNodeById( $targetParentId );

        // new placement for node
        $newParent->children[$nodeId] = $nodeToMove;

        // remove old location from previous parent
        unset( $nodeToMove->parent->children[$nodeId] );

        // update parent attribute of the node
        $nodeToMove->parent = $newParent;
    }

    /**
     * Fixates the transaction.
     */
    public function fixateTransaction()
    {
    }
}
?>
