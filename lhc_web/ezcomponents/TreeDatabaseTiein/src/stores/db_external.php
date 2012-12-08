<?php
/**
 * File containing the ezcTreeDbExternalTableDataStore class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package TreeDatabaseTiein
 */

/**
 * ezcTreeDbExternalTableDataStore is an implementation of a tree node
 * data store that uses an external table to store data in.
 *
 * @package TreeDatabaseTiein
 * @version 1.1.1
 * @mainclass
 */
class ezcTreeDbExternalTableDataStore implements ezcTreeDbDataStore, ezcTreeXmlDataStore
{
    /**
     * Contains the database connection handler.
     *
     * @var ezcDbHandler
     */
    private $dbHandler;

    /**
     * Contains the name of the table to fetch data from.
     *
     * @var string
     */
    private $table = null;

    /**
     * Contains the name of the field that contains the node ID.
     *
     * @var string
     */
    private $idField = null;

    /**
     * Contains the name of the field to fetch data from.
     *
     * If this field is null, then the whole row is returned.
     *
     * @var string
     */
    private $dataField = null;

    /**
     * Contains the DOM representing this tree this data store stores data for.
     *
     * @var DOMDocument
     */
    protected $dom;

    /**
     * Constructs a new storage backend that stores data in a table external
     * from the node tree.
     *
     * The store will use the database connection specified by $dbHandler, and
     * the table $dataTable to store the data in. The lookup field that is matched
     * against the node ID is specified with $idField. By default the store will
     * return the whole row unless a specific field has been configured through
     * the $dataField argument in this constructor.
     *
     * @param ezcDbHandler $dbHandler
     * @param string $dataTable
     * @param string $idField
     * @param string $dataField
     */
    public function __construct( ezcDbHandler $dbHandler, $dataTable, $idField, $dataField = null )
    {
        $this->dbHandler = $dbHandler;
        $this->table = $dataTable;
        $this->idField = $idField;
        $this->dataField = $dataField;
    }

    /**
     * Deletes the data for the node $node from the data store.
     *
     * @param ezcTreeNode $node
    public function deleteDataForNode( ezcTreeNode $node )
    {
    }
     */

    /**
     * Deletes the data for all the nodes in the node list $nodeList.
     *
     * @param ezcTreeNodeList $nodeList
     */
    public function deleteDataForNodes( ezcTreeNodeList $nodeList )
    {
        $nodeIdsToDelete = array();
        foreach ( array_keys( $nodeList->nodes ) as $id )
        {
            $nodeIdsToDelete[] = (string) $id;
        }

        $db = $this->dbHandler;
        $q = $db->createDeleteQuery();
        $q->deleteFrom( $db->quoteIdentifier( $this->table ) )
          ->where( $q->expr->in( $db->quoteIdentifier( $this->idField ), $nodeIdsToDelete ) );
        $s = $q->prepare();
        $s->execute();
    }

    /**
     * Deletes the data for all the nodes in the store.
     */
    public function deleteDataForAllNodes()
    {
        $db = $this->dbHandler;
        $q = $db->createDeleteQuery();

        $q->deleteFrom( $db->quoteIdentifier( $this->table ) );
        $s = $q->prepare();
        $s->execute();
    }

    /**
     * Takes the data from the executed query and uses the $dataField 
     * property to filter out the wanted data for this node.
     *
     * @param array $data
     * @return mixed
     */
    private function filterDataFromResult( array $data )
    {
        if ( $this->dataField === null )
        {
            unset( $data[$this->idField] );
            return $data;
        }
        return $data[$this->dataField];
    }

    /**
     * Retrieves the data for the node $node from the data store and assigns it
     * to the node's 'data' property.
     *
     * @param ezcTreeNode $node
     */
    public function fetchDataForNode( ezcTreeNode $node )
    {
        $db = $this->dbHandler;
        $q = $db->createSelectQuery();

        $id = $node->id;
        $q->select( '*' )
          ->from( $db->quoteIdentifier( $this->table ) )
          ->where( $q->expr->eq( $db->quoteIdentifier( $this->idField ), $q->bindValue( $id ) ) );
        $s = $q->prepare();
        $s->execute();

        $result = $s->fetch( PDO::FETCH_ASSOC );
        if ( !$result )
        {
            throw new ezcTreeDataStoreMissingDataException( $node->id );
        }
        $node->data = $this->filterDataFromResult( $result );
        $node->dataFetched = true;
    }

    /**
     * This method *tries* to fetch the data for all the nodes in the node list
     * $nodeList and assigns this data to the nodes' 'data' properties.
     *
     * @param ezcTreeNodeList $nodeList
     */
    public function fetchDataForNodes( ezcTreeNodeList $nodeList )
    {
        $nodeIdsToFetch = array();
        foreach ( $nodeList->nodes as $node )
        {
            if ( $node->dataFetched === false )
            {
                $nodeIdsToFetch[] = $node->id;
            }
        }
        if ( count( $nodeIdsToFetch ) === 0 )
        {
            return;
        }

        $db = $this->dbHandler;
        $q = $db->createSelectQuery();

        $q->select( '*' )
          ->from( $db->quoteIdentifier( $this->table ) )
          ->where( $q->expr->in( $db->quoteIdentifier( $this->idField ), $nodeIdsToFetch ) );
        $s = $q->prepare();
        $s->execute();

        foreach ( $s as $result )
        {
            $nodeList[$result[$this->idField]]->data = $this->filterDataFromResult( $result );
            $nodeList[$result[$this->idField]]->dataFetched = true;
        }
    }

    /**
     * Stores the data in the node to the data store.
     *
     * @param ezcTreeNode $node
     */
    public function storeDataForNode( ezcTreeNode $node )
    {
        $db = $this->dbHandler;

        // first we check if there is data for this node
        $id = $node->id;
        $q = $db->createSelectQuery();
        $q->select( $db->quoteIdentifier( $this->idField ) )
          ->from( $db->quoteIdentifier( $this->table ) )
          ->where( $q->expr->eq( $db->quoteIdentifier( $this->idField ), $q->bindValue( $id ) ) );
        $s = $q->prepare();
        $s->execute();

        $update = $s->fetch();
        if ( !$update ) // we don't have data yet, create an insert query
        {
            $q = $db->createInsertQuery();
            $q->insertInto( $db->quoteIdentifier( $this->table ) )
              ->set( $db->quoteIdentifier( $this->idField ), $q->bindValue( $node->id ) );
        }
        else // we have data, so use update
        {
            $q = $db->createUpdateQuery();
            $q->update( $db->quoteIdentifier( $this->table ) );
        }

        // Add set statements
        if ( $this->dataField === null )
        {
            foreach ( $node->data as $field => $value )
            {
               $q->set( $db->quoteIdentifier( $field ), $q->bindValue( $value ) );
            }
        }
        else
        {
            $q->set( $db->quoteIdentifier( $this->dataField ), $q->bindValue( $node->data ) );
        }

        if ( $update ) // add where clause if we're updating
        {
            $q->where( $q->expr->eq( $db->quoteIdentifier( $this->idField ), $q->bindValue( $id ) ) );
        }
        $s = $q->prepare();
        $s->execute();
        $node->dataStored = true;
    }

    /**
     * Associates the DOM tree for which this data store stores data for with
     * this store.
     *
     * This method is only needed for when a data store is used
     * with an XML based tree backend. XML based tree backends call this method
     * to associate the DOM tree with the store. This is not needed for this
     * data store so the method is a no-op.
     *
     * @param DOMDocument $dom
     */
    public function setDomTree( DOMDocument $dom )
    {
    }
}
?>
