<?php
/**
 * File containing the ezcTreeDbMaterializedPath class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package TreeDatabaseTiein
 */

/**
 * ezcTreeDbMaterializedPath implements a tree backend which stores parent/child
 * information in a path like string (such as /1/4/6/8).
 *
 * The table that stores the index (configured using the $indexTableName argument
 * of the {@link __construct} method) should contain at least three fields. The
 * first one 'id' will contain the node's ID, the second one 'parent_id' the ID
 * of the node's parent. Both fields should be of the same database field type.
 * Supported field types are either integer or a string type. The third field
 * 'path' will contain the path string. This should be a text field. The size
 * of the field determines the maximum depth the tree can have.
 * In order to use auto-generated IDs, the 'id' field needs to be an
 * auto-incrementing integer field, by using either an auto-increment field, or
 * a sequence.
 *
 * @property-read ezcTreeDbDataStore $store
 *                The data store that is used for retrieving/storing data.
 * @property-read string             $separationChar
 *                The character that is used to separate node IDs internally.
 *                This character can then *not* be part of a node ID.
 * @property      string $nodeClassName
 *                Which class is used as tree node - this class *must* inherit
 *                the ezcTreeNode class.
 *
 * @package TreeDatabaseTiein
 * @version 1.1.1
 * @mainclass
 */
class ezcTreeDbMaterializedPath extends ezcTreeDb
{
    /**
     * Constructs a new ezcTreeDbMaterializedPath object.
     *
     * The different arguments to the constructor configure which database
     * connection ($dbh) is used to access the database and the $indexTableName
     * argument which table is used to retrieve the relation data from. The
     * $store argument configure which data store is used with this tree.
     *
     * The $separationChar argument defaults to / and is used to separate node
     * IDs internally. This character can *not* be part of a node ID, and should be
     * the same character that was used when creating the tree.
     *
     * Just like the others, this database backend requires the index table to
     * at least define the field 'id', which can either be a string or an
     * integer field.
     * 
     * @param ezcDbHandler       $dbh
     * @param string             $indexTableName
     * @param ezcTreeDbDataStore $store
     * @param string             $separationChar
     */
    public function __construct( ezcDbHandler $dbh, $indexTableName, ezcTreeDbDataStore $store, $separationChar = '/' )
    {
        parent::__construct( $dbh, $indexTableName, $store );
        $this->properties['separationChar'] = $separationChar;
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
            case 'separationChar':
                return $this->properties[$name];
        }
        return parent::__get( $name );
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
            case 'separationChar':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                return parent::__set( $name, $value );
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
            case 'separationChar':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
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
        if ( strchr( $nodeId, $this->properties['separationChar'] ) != false )
        {
            throw new ezcTreeInvalidIdException( $nodeId, $this->properties['separationChar'] );
        }
    }

    /**
     * Creates a new ezcTreeDbMaterializedPath object.
     *
     * The different arguments to the method configure which database
     * connection ($dbh) is used to access the database and the $indexTableName
     * argument which table is used to retrieve the relation data from. The
     * $store argument configure which data store is used with this tree.
     *
     * The $separationChar argument defaults to / and is used to separate node
     * IDs internally. This character can *not* be part of a node ID, and the same
     * character should be used when re-opening the tree upon instantiation.
     *
     * It is up to the user to create the database table and make sure it is
     * empty.
     * 
     * @param ezcDbHandler       $dbh
     * @param string             $indexTableName
     * @param ezcTreeDbDataStore $store
     * @param string             $separationChar
     */
    public static function create( ezcDbHandler $dbh, $indexTableName, ezcTreeDbDataStore $store, $separationChar = '/' )
    {
        return new ezcTreeDbMaterializedPath( $dbh, $indexTableName, $store, $separationChar );
    }

    /**
     * Returns the parent id and path the node with ID $nodeId as an array.
     *
     * The format of the array is:
     * - 0: parent id
     * - 1: path
     *
     * @param string $nodeId
     * @return array(int)
     */
    protected function fetchNodeInformation( $nodeId )
    {
        $db = $this->dbh;

        // SELECT parent_id, path
        // FROM indexTable
        // WHERE id = $nodeId
        $q = $db->createSelectQuery();
        $q->select( 'parent_id, path' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->eq( 'id', $q->bindValue( $nodeId ) ) );
        $s = $q->prepare();
        $s->execute();
        $r = $s->fetchAll( PDO::FETCH_NUM );
        return $r[0];
    }

    /**
     * Runs SQL to get all the children of the node with ID $nodeId as a PDO
     * result set.
     *
     * @param string $nodeId
     * @return PDOStatement
     */
    protected function fetchChildRecords( $nodeId )
    {
        $db = $this->dbh;
        $q = $db->createSelectQuery();

        // SELECT id, parent_id
        // FROM indexTable
        // WHERE parent_id = $nodeId
        $q->select( 'id, parent_id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->eq( 'parent_id', $q->bindValue( $nodeId ) ) );

        $s = $q->prepare();
        $s->execute();
        return $s;
    }

    /**
     * Returns all the children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchChildren( $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;
        foreach ( $this->fetchChildRecords( $nodeId ) as $record )
        {
            $list->addNode( new $className( $this, $record['id'] ) );
        }
        return $list;
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

        // Fetch node information
        list( $parentId, $path ) = $this->fetchNodeInformation( $nodeId );

        $parts = explode( $this->properties['separationChar'], $path );
        array_shift( $parts );

        foreach ( $parts as $pathNodeId )
        {
            $list->addNode( new $className( $this, $pathNodeId ) );
            $pathNodeId = $this->getParentId( $pathNodeId );
        }

        $list->addNode( new $className( $this, $nodeId ) );
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
        $list->addNode( new $className( $this, $nodeId ) );

        // Fetch information for node
        list( $parentId, $path ) = $this->fetchNodeInformation( $nodeId );

        $db = $this->dbh;
        $q = $db->createSelectQuery();

        // SELECT id
        // FROM materialized_path
        // WHERE path LIKE '$path/%'
        $q->select( 'id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->like( 'path', $q->bindValue( "$path{$this->properties['separationChar']}%" ) ) );
        $s = $q->prepare();
        $s->execute();

        foreach ( $s as $record )
        {
            $list->addNode( new $className( $this, $record['id'] ) );
        }

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
     * Adds the children nodes of the node with ID $nodeId to the
     * ezcTreeNodeList $list.
     *
     * @param ezcTreeNodeList $list
     * @param string          $nodeId
     */
    protected function addChildNodesBreadthFirst( ezcTreeNodeList $list, $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $childRecords = $this->fetchChildRecords( $nodeId )->fetchAll();
        foreach ( $childRecords as $record )
        {
            $list->addNode( new $className( $this, $record['id'] ) );
        }
        foreach ( $childRecords as $record )
        {
            $this->addChildNodesBreadthFirst( $list, $record['id'] );
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
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;
        $list->addNode( new $className( $this, $nodeId ) );
        $this->addChildNodesBreadthFirst( $list, $nodeId );
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
        $db = $this->dbh;
        $q = $db->createSelectQuery();

        // SELECT count(id)
        // FROM indexTable
        // WHERE parent_id = $nodeId
        $q->select( 'count(id)' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->eq( 'parent_id', $q->bindValue( $nodeId ) ) );

        $s = $q->prepare();
        $s->execute();
        return (int) $s->fetchColumn( 0 );
    }

    /**
     * Returns the number of children of the node with ID $nodeId, recursively.
     *
     * @param string $nodeId
     * @return int
     */
    public function getChildCountRecursive( $nodeId )
    {
        // Fetch information for node
        list( $parentId, $path ) = $this->fetchNodeInformation( $nodeId );

        $db = $this->dbh;
        $q = $db->createSelectQuery();

        // SELECT count(id)
        // FROM materialized_path
        // WHERE path LIKE '$path/%'
        $q->select( 'count(id)' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->like( 'path', $q->bindValue( "$path{$this->properties['separationChar']}%" ) ) );
        $s = $q->prepare();
        $s->execute();
        $r = $s->fetch( PDO::FETCH_NUM );

        return (int) $r[0];
    }

    /**
     * Returns the distance from the root node to the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    public function getPathLength( $nodeId )
    {
        // Fetch information for node
        list( $parentId, $path ) = $this->fetchNodeInformation( $nodeId );

        return substr_count( $path, $this->properties['separationChar'] ) - 1;
    }

    /**
     * Returns whether the node with ID $nodeId has children.
     *
     * @param string $nodeId
     * @return bool
     */
    public function hasChildNodes( $nodeId )
    {
        return $this->getChildCount( $nodeId ) > 0;
    }

    /**
     * Returns whether the node with ID $childId is a direct child of the node
     * with ID $parentId.
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    public function isChildOf( $childId, $parentId )
    {
        $nodeId = $this->getParentId( $childId );
        $parentId = (string) $parentId;
        return $parentId === $nodeId;
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
        // Fetch node information
        list( $dummyParentId, $path ) = $this->fetchNodeInformation( $childId );

        $parts = explode( $this->properties['separationChar'], $path );
        array_shift( $parts );

        return in_array( $parentId, $parts ) && ( $childId !== $parentId );
    }

    /**
     * Returns whether the nodes with IDs $child1Id and $child2Id are siblings
     * (ie, they share the same parent).
     *
     * @param string $child1Id
     * @param string $child2Id
     * @return bool
     */
    public function isSiblingOf( $child1Id, $child2Id )
    {
        $nodeId1 = $this->getParentId( $child1Id );
        $nodeId2 = $this->getParentId( $child2Id );
        return $nodeId1 === $nodeId2 && (string) $child1Id !== (string) $child2Id;
    }

    /**
     * Sets a new node as root node, this also wipes out the whole tree.
     *
     * @param ezcTreeNode $node
     */
    public function setRootNode( ezcTreeNode $node )
    {
        $db = $this->dbh;

        $q = $db->createDeleteQuery();
        $q->deleteFrom( $db->quoteIdentifier( $this->indexTableName ) );
        $s = $q->prepare();
        $s->execute();
        $this->store->deleteDataForAllNodes();

        $q = $db->createInsertQuery();
        $q->insertInto( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'parent_id', "null" )
          ->set( 'id', $q->bindValue( $node->id ) )
          ->set( 'path', $q->bindValue( $this->properties['separationChar'] . $node->id ) );
        $s = $q->prepare();
        $s->execute();

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
          ->set( 'path', 0 );

        return $q;
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

        // Fetch parent information
        list( $parentParentId, $path ) = $this->fetchNodeInformation( $parentId );

        $q = $this->createAddNodeQuery( $childNode->id );
        $q->set( 'parent_id', $q->bindValue( $parentId ) )
          ->set( 'id', $q->bindValue( $childNode->id ) )
          ->set( 'path', $q->bindValue( $path . $this->properties['separationChar'] . $childNode->id ) );
        $s = $q->prepare();
        $s->execute();

        $this->store->storeDataForNode( $childNode, $childNode->data );
    }

    /**
     * Deletes all nodes in the node list $list.
     *
     * @param ezcTreeNodeList $list
     */
    private function deleteNodes( ezcTreeNodeList $list )
    {
        $db = $this->dbh;
        $q = $db->createDeleteQuery();

        $nodeIdList = array();
        foreach ( array_keys( $list->nodes ) as $nodeId )
        {
            $nodeIdList[] = (string) $nodeId;
        }

        // DELETE FROM indexTable
        // WHERE id in ( $list );
        $q->deleteFrom( $db->quoteIdentifier( $this->indexTableName ) );
        $q->where( $q->expr->in( 'id', $nodeIdList ) );
        $s = $q->prepare();
        $s->execute();
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

        $nodeList = $this->fetchSubtree( $nodeId );
        $this->deleteNodes( $nodeList );
        $this->store->deleteDataForNodes( $nodeList );
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

        list( $origParentId, $origPath ) = $this->fetchNodeInformation( $nodeId );
        list( $targetParentParentId, $targetParentPath ) = $this->fetchNodeInformation( $targetParentId );

        // Get path to parent of $nodeId
        // - position of last /
        $pos = strrpos( $origPath, $this->properties['separationChar'] );
        // - parent path and its length
        $parentPath = substr( $origPath, 0, $pos );
        $parentPathLength = strlen( $parentPath ) + 1;

        $db = $this->dbh;

        // Update parent ID in node $nodeId
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'parent_id', $q->bindValue( $targetParentId ) )
          ->where( $q->expr->eq( 'id', $q->bindValue( $nodeId ) ) );
        $s = $q->prepare();
        $s->execute();

        // Update paths for subtree
        // UPDATE indexTable
        // SET path = $targetParentPath || SUBSTR( path, $parentPathLength ) )
        // WHERE id = $nodeId
        //    OR path LIKE '$origPath/%'
        $q = $db->createUpdateQuery();
        $q->update( $db->quoteIdentifier( $this->indexTableName ) )
          ->set( 'path', $q->expr->concat(
                             $q->bindValue( $targetParentPath ),
                             $q->expr->subString( 'path', $q->bindValue( $parentPathLength ) )
            ) )
          ->where( $q->expr->lOr(
                $q->expr->eq( 'id', $q->bindValue( $nodeId ) ),
                $q->expr->like( 'path', $q->bindValue( "$origPath{$this->properties['separationChar']}%" ) )
            ) );
        $s = $q->prepare();
        $s->execute();
    }

    /**
     * Fixates the transaction.
     */
    public function fixateTransaction()
    {
    }
}
?>
