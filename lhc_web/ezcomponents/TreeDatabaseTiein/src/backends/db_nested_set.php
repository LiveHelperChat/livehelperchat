<?php
/**
 * File containing the ezcTreeDbNestedSet class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package TreeDatabaseTiein
 */

/**
 * ezcTreeDbNestedSet implements a tree backend which stores parent/child
 * information with left and right values.
 *
 * The table that stores the index (configured using the $indexTableName argument
 * of the {@link __construct} method) should contain at least four fields. The
 * first one 'id' will contain the node's ID, the second one 'parent_id' the ID
 * of the node's parent. Both fields should be of the same database field type.
 * Supported field types are either integer or a string type.  The other two
 * fields "lft" and "rgt" will store the left and right values that the
 * algorithm requires. These two fields should be of an integer type.
 * In order to use auto-generated IDs, the 'id' field needs to be an
 * auto-incrementing integer field, by using either an auto-increment field, or
 * a sequence.
 *
 * @property-read ezcTreeDbDataStore $store
 *                The data store that is used for retrieving/storing data.
 * @property      string $nodeClassName
 *                Which class is used as tree node - this class *must* inherit
 *                the ezcTreeNode class.
 *
 * @package TreeDatabaseTiein
 * @version 1.1.1
 * @mainclass
 */
class ezcTreeDbNestedSet extends ezcTreeDbParentChild
{
    /**
     * Creates a new ezcTreeDbNestedSet object.
     *
     * The different arguments to the method configure which database
     * connection ($dbh) is used to access the database and the $indexTableName
     * argument which table is used to retrieve the relation data from. The
     * $store argument configure which data store is used with this tree.
     *
     * It is up to the user to create the database table and make sure it is
     * empty.
     * 
     * @param ezcDbHandler       $dbh
     * @param string             $indexTableName
     * @param ezcTreeDbDataStore $store
     */
    public static function create( ezcDbHandler $dbh, $indexTableName, ezcTreeDbDataStore $store )
    {
        return new ezcTreeDbNestedSet( $dbh, $indexTableName, $store );
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
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;

        $db = $this->dbh;
        $q = $db->createSelectQuery();

        // SELECT parent.id
        // FROM indexTable as node,
        //      indexTable as parent
        // WHERE
        //     node.lft BETWEEN parent.lft AND parent.rgt
        //     AND
        //     node.if = $nodeId
        // ORDER BY parent.lft
        $q->select( 'parent.id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) . " as node" )
          ->from( $db->quoteIdentifier( $this->indexTableName ) . " as parent" )
          ->where( $q->expr->lAnd(
              $q->expr->between( 'node.lft', 'parent.lft', 'parent.rgt' ),
              $q->expr->eq( 'node.id', $q->bindValue( $nodeId ) )
            ))
          ->orderBy( 'parent.lft' );

        $s = $q->prepare();
        $s->execute();

        foreach ( $s as $result )
        {
            $list->addNode( new $className( $this, $result['id'] ) );
        }
        return $list;
    }

    /**
     * Returns the node with ID $nodeId and all its children, sorted according to
     * the {@link http://en.wikipedia.org/wiki/Depth-first_search Depth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeDepthFirst( $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;
        $db = $this->dbh;

        // Fetch parent information
        list( $left, $right, $width ) = $this->fetchNodeInformation( $nodeId );

        // Fetch subtree
        //   SELECT id
        //   FROM indexTable
        //   WHERE lft BETWEEN $left AND $right
        //   ORDER BY lft
        $q = $db->createSelectQuery();
        $q->select( 'id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->between( 'lft', $q->bindValue( $left ), $q->bindValue( $right ) ) )
          ->orderBy( 'lft' );
        $s = $q->prepare();
        $s->execute();

        foreach ( $s as $result )
        {
            $list->addNode( new $className( $this, $result['id'] ) );
        }
        return $list;
    }

    /**
     * Returns the distance from the root node to the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    public function getPathLength( $nodeId )
    {
        $path = $this->fetchPath( $nodeId );
        return count( $path->nodes ) - 1;
    }

    /**
     * Returns whether the node with ID $childId is a direct or indirect child
     * of the node with ID $parentId.
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    public function isDescendantOf( $childId, $parentId )
    {
        $path = $this->fetchPath( $childId );

        if ( isset( $path[$parentId] ) && ( $childId !== $parentId ) )
        {
            return true;
        }
        return false;
    }

    /**
     * Sets a new node as root node, this also wipes out the whole tree.
     *
     * @param ezcTreeNode $node
     */
    public function setRootNode( ezcTreeNode $node )
    {
        $db = $this->dbh;

        // Remove nodes from tree
        //   DELETE FROM indexTable
        $q = $db->createDeleteQuery();
        $q->deleteFrom( $db->quoteIdentifier( $this->indexTableName ) );
        $s = $q->prepare();
        $s->execute();

        // Remove all data belonging to those nodes
        $this->store->deleteDataForAllNodes();

        // Create new root node
        //   INSERT INTO indexTable
        //   SET parent_id = null,
        //       id = $node->id,
        //       lft = 1, rgt = 2
        $q = $db->createInsertQuery();
        $q->insertInto( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'parent_id', "null" )
          ->set( 'id', $q->bindValue( $node->id ) )
          ->set( 'lft', 1 )
          ->set( 'rgt', 2 );
        $s = $q->prepare();
        $s->execute();

        // Store data for new root node
        $this->store->storeDataForNode( $node, $node->data );
    }

    /**
     * Creates the query to insert an empty node into the database, so that the last-inserted ID can be obtained.
     *
     * @return ezcQueryInsert
     */
    protected function createAddEmptyNodeQuery()
    {
        $db = $this->dbh;

        $q = $db->createInsertQuery();
        $q->insertInto( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'lft', 0 )
          ->set( 'rgt', 0 );

        return $q;
    }

    /**
     * Updates the left and right values of the nodes that are added while
     * adding a whole subtree as child of a node.
     *
     * The method does not update nodes where the IDs are in the $excludedIds
     * list.
     *
     * @param int $right
     * @param int $width
     * @param array(string) $excludedIds
     */
    protected function updateNestedValuesForSubtreeAddition( $right, $width, $excludedIds = array() )
    {
        $db = $this->dbh;

        // Move all the right values + $width for nodes where the the right value >=
        // the parent right value:
        //   UPDATE indexTable
        //   SET rgt = rgt + $width
        //   WHERE rgt >= $right
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'rgt', $q->expr->add( 'rgt', $width ) )
          ->where( $q->expr->gte( 'rgt', $right ) );
        if ( count( $excludedIds ) )
        {
            $q->where( $q->expr->not( $q->expr->in( 'id', $excludedIds ) ) );
        }
        $q->prepare()->execute();

        // Move all the left values + $width for nodes where the the right value >=
        // the parent left value
        //   UPDATE indexTable
        //   SET lft = lft + $width
        //   WHERE lft >= $right
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'lft', $q->expr->add( 'lft', $width ) )
          ->where( $q->expr->gte( 'lft', $right ) );
        if ( count( $excludedIds ) )
        {
            $q->where( $q->expr->not( $q->expr->in( 'id', $excludedIds ) ) );
        }
        $q->prepare()->execute();
    }

    /**
     * Adds the node $childNode as child of the node with ID $parentId.
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

        $db = $this->dbh;

        // Fetch parent's information
        list( $left, $right, $width ) = $this->fetchNodeInformation( $parentId );

        // Update left and right values to account for new subtree
        $this->updateNestedValuesForSubtreeAddition( $right, 2 );

        // Add new node
        if ( $width == 2 )
        {
            $newLeft = $left + 1;
            $newRight = $left + 2;
        }
        else
        {
            $newLeft = $right;
            $newRight = $right + 1;
        }

        // INSERT INTO indexTable
        // SET parent_id = $parentId,
        //     id = $childNode->id,
        //     lft = $newLeft,
        //     rgt = $newRight
        $q = $this->createAddNodeQuery( $childNode->id );
        $q->set( 'parent_id', $q->bindValue( $parentId ) )
          ->set( 'id', $q->bindValue( $childNode->id ) )
          ->set( 'lft', $q->bindValue( $newLeft ) )
          ->set( 'rgt', $q->bindValue( $newRight ) );
        $s = $q->prepare();
        $s->execute();

        // Add the data for the new node
        $this->store->storeDataForNode( $childNode, $childNode->data );
    }

    /**
     * Returns the left, right and width values for the node with ID $nodeId as an
     * array.
     *
     * The format of the array is:
     * - 0: left value
     * - 1: right value
     * - 2: width value (right - left + 1)
     *
     * @param string $nodeId
     * @return array(int)
     */
    protected function fetchNodeInformation( $nodeId )
    {
        $db = $this->dbh;

        // SELECT lft, rgt, rft-lft+1 as width
        // FROM indexTable
        // WHERE id = $nodeId
        $q = $db->createSelectQuery();
        $q->select( 'lft, rgt, rgt - lft + 1 as width' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->eq( 'id', $q->bindValue( $nodeId ) ) );
        $s = $q->prepare();
        $s->execute();
        $r = $s->fetchAll( PDO::FETCH_NUM );
        return $r[0];
    }

    /**
     * Updates the left and right values in case a subtree is deleted.
     *
     * @param int $right
     * @param int $width
     */
    protected function updateNestedValuesForSubtreeDeletion( $right, $width )
    {
        $db = $this->dbh;

        // Move all the right values + $width for nodes where the the right
        // value > the parent right value
        //   UPDATE indexTable
        //   SET rgt = rgt - $width
        //   WHERE rgt > $right
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'rgt', $q->expr->sub( 'rgt', $width ) )
          ->where( $q->expr->gt( 'rgt', $right ) );
        $q->prepare()->execute();

        // Move all the right values + $width for nodes where the the left
        // value > the parent right value
        //   UPDATE indexTable
        //   SET lft = lft - $width
        //   WHERE lft > $right
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'lft', $q->expr->sub( 'lft', $width ) )
          ->where( $q->expr->gt( 'lft', $right ) );
        $q->prepare()->execute();
    }

    /**
     * Deletes the node with ID $nodeId from the tree, including all its children.
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

        // Delete all data for the deleted nodes
        $nodeList = $this->fetchSubtreeDepthFirst( $nodeId );
        $this->store->deleteDataForNodes( $nodeList );

        // Fetch node information
        list( $left, $right, $width ) = $this->fetchNodeInformation( $nodeId );

        // DELETE FROM indexTable
        // WHERE lft BETWEEN $left and $right
        $db = $this->dbh;
        $q = $db->createDeleteQuery();
        $q->deleteFrom( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->between( 'lft', $left, $right ) );
        $s = $q->prepare();
        $s->execute();

        // Update the left and right values to account for the removed subtree
        $this->updateNestedValuesForSubtreeDeletion( $right, $width );
    }

    /**
     * Moves the node with ID $nodeId as child to the node with ID $targetParentId.
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

        $db = $this->dbh;

        // Get the nodes that are gonne be moved in the subtree
        $nodeIds = array();
        foreach ( $this->fetchSubtreeDepthFirst( $nodeId )->nodes as $node )
        {
            $nodeIds[] = $node->id;
        }

        // Update parent ID for the node
        //   UPDATE indexTable
        //   SET parent_id = $targetParentId
        //   WHERE id = $nodeId
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'parent_id', $q->bindValue( $targetParentId ) )
          ->where( $q->expr->eq( 'id', $q->bindValue( $nodeId ) ) );

        $s = $q->prepare();
        $s->execute();

        // Fetch node information
        list( $origLeft, $origRight, $origWidth ) = $this->fetchNodeInformation( $nodeId );

        // Update the nested values to account for the moved subtree (delete part)
        $this->updateNestedValuesForSubtreeDeletion( $origRight, $origWidth );

        // Fetch node information
        list( $targetParentLeft, $targetParentRight, $targerParentWidth ) = $this->fetchNodeInformation( $targetParentId );

        // Update the nested values to account for the moved subtree (addition part)
        $this->updateNestedValuesForSubtreeAddition( $targetParentRight, $origWidth, $nodeIds );

        // Update nodes in moved subtree
        $adjust = $targetParentRight - $origLeft;

        // UPDATE indexTable
        // SET rgt = rgt + $adjust
        // WHERE id in $nodeIds
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'rgt', $q->expr->add( 'rgt', $adjust ) )
          ->where( $q->expr->in( 'id', $nodeIds ) );
        $q->prepare()->execute();

        // UPDATE indexTable
        // SET lft = lft + $adjust
        // WHERE id in $nodeIds
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'lft', $q->expr->add( 'lft', $adjust ) )
          ->where( $q->expr->in( 'id', $nodeIds ) );
        $q->prepare()->execute();
    }
}
?>
