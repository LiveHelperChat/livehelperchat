<?php
/**
 * File containing the ezcTreeDb class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package TreeDatabaseTiein
 */

/**
 * ezcTreeDb contains common methods for the different database tree backends.
 *
 * @property-read ezcTreeDbDataStore $store
 *                The data store that is used for retrieving/storing data.
 * @property      string $nodeClassName
 *                Which class is used as tree node - this class *must* inherit
 *                the ezcTreeNode class.
 *
 * @package TreeDatabaseTiein
 * @version 1.1.1
 */
abstract class ezcTreeDb extends ezcTree
{
    /**
     * Contains the database connection handler.
     *
     * @var ezcDbHandler
     */
    protected $dbh;

    /**
     * Contains the name of the table to retrieve the relational data from.
     *
     * @var string
     */
    protected $indexTableName;

    /**
     * Constructs a new ezcTreeDb object.
     *
     * The different arguments to the constructor configure which database
     * connection ($dbh) is used to access the database and the $indexTableName
     * argument which table is used to retrieve the relation data from. The
     * $store argument configure which data store is used with this tree.
     *
     * All database backends require the index table to at least define the
     * field 'id', which can either be a string or an integer field.
     * 
     * @param ezcDbHandler       $dbh
     * @param string             $indexTableName
     * @param ezcTreeDbDataStore $store
     */
    public function __construct( ezcDbHandler $dbh, $indexTableName, ezcTreeDbDataStore $store )
    {
        $this->dbh = $dbh;
        $this->indexTableName = $indexTableName;
        $this->properties['store'] = $store;
        $this->properties['autoId'] = false;
    }

    /**
     * Creates the query to insert an empty node into the database, so that the last-inserted ID can be obtained.
     *
     * @return ezcQueryInsert
     */
    abstract protected function createAddEmptyNodeQuery();

    /**
     * Creates the query to insert/update an empty node in the database.
     *
     * The query is constructed for the child with ID $id
     *
     * @param mixed $id
     * @return ezcQuery
     */
    protected function createAddNodeQuery( $id )
    {
        $db = $this->dbh;

        if ( $this->properties['autoId'] )
        {
            $q = $db->createUpdateQuery();
            $q->update( $db->quoteIdentifier( $this->indexTableName ) )
              ->where( $q->expr->eq( 'id', $q->bindValue( $id ) ) );
        }
        else
        {
            $q = $db->createInsertQuery();
            $q->insertInto( $db->quoteIdentifier( $this->indexTableName ) );
        }
        return $q;
    }

    /**
     * This method generates the next node ID.
     *
     * It does so by inserting a new empty node into the database, and uses
     * lastInsertId() to obtain the ID for the newly inserted node.
     *
     * @return integer
     */
    protected function generateNodeID()
    {
        $db = $this->dbh;
        $q = $this->createAddEmptyNodeQuery();

        $s = $q->prepare();

        try
        {
            $s->execute();
        }
        catch ( PDOException $e )
        {
            throw new ezcTreeDbInvalidSchemaException( "generating a new node ID", $e->getMessage() );
        }

        $r = $db->lastInsertId( $this->indexTableName . '_id_seq' );
        return $r;
    }

    /**
     * Returns whether the node with ID $id exists as tree node.
     *
     * @param string $id
     * @return bool
     */
    public function nodeExists( $id )
    {
        $db = $this->dbh;
        $q = $db->createSelectQuery();

        $q->select( 'id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->eq( 'id', $q->bindValue( $id ) ) );

        $s = $q->prepare();
        $s->execute();

        return count( $s->fetchAll() ) ? true : false;
    }

    /**
     * Returns the ID of parent of the node with ID $childId.
     *
     * @param string $childId
     * @return string
     */
    protected function getParentId( $childId )
    {
        $db = $this->dbh;
        $q = $db->createSelectQuery();

        $q->select( 'id, parent_id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->eq( 'id', $q->bindValue( $childId ) ) );

        $s = $q->prepare();
        $s->execute();
        $row = $s->fetch();
        return $row['parent_id'];
    }

    /**
     * Returns the parent node of the node with ID $id.
     *
     * This method returns null if there is no parent node.
     *
     * @param string $id
     * @return ezcTreeNode
     */
    public function fetchParent( $id )
    {
        $className = $this->properties['nodeClassName'];
        $parentId = $this->getParentId( $id );
        return $parentId !== null ? new $className( $this, $parentId ) : null;
    }

    /**
     * Returns the root node.
     *
     * This methods returns null if there is no root node.
     *
     * @return ezcTreeNode
     */
    public function getRootNode()
    {
        $className = $this->properties['nodeClassName'];
        $db = $this->dbh;

        // SELECT id
        // FROM indexTable
        // WHERE id IS null
        $q = $db->createSelectQuery();
        $q->select( 'id' )
          ->from( $db->quoteIdentifier( $this->indexTableName ) )
          ->where( $q->expr->isNull( 'parent_id' ) );
        $s = $q->prepare();
        $s->execute();
        $r = $s->fetchAll( PDO::FETCH_ASSOC );
        if ( count( $r ) )
        {
            return new $className( $this, $r[0]['id'] );
        }
        return null;
    }
}
?>
