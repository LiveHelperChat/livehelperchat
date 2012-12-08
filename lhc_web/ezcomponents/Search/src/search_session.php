<?php
/**
 * File containing the ezcSearchSession class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcSearchSession is the main runtime interface for searching documents.
 *
 * @property-read ezcSearchHandler $handler
 *                The handler set in the constructor.
 * @property-read ezcSearchDefinitionManager $definitionManager
 *                The persistent definition manager set in the constructor.
 *
 * @package Search
 * @version 1.0.9
 * @mainclass
 */
class ezcSearchSession
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Constructs a new search session that works on the handler $handler.
     *
     * The $manager provides valid search document definitions to the
     * session. The $handler will be used to perform all search operations.
     *
     * @param ezcSearchHandler $handler
     * @param ezcSearchDefinitionManager $manager
     */
    public function __construct( ezcSearchHandler $handler, ezcSearchDefinitionManager $manager )
    {
        $this->properties['handler']           = $handler;
        $this->properties['definitionManager'] = $manager;
    }

    /**
     * Returns the result of the search query $query as a list of objects.
     *
     * Returns the documents found for document type $type using the submitted
     * $query. $query should be created using {@link createFindQuery()}.
     *
     * Example:
     * <code>
     * $q = $session->createFindQuery();
     * $allPersons = $session->find( $q );
     * </code>
     *
     * @throws ezcSearchDefinitionNotFoundException
     *         if there is no such persistent class.
     * @throws ezcSearchQueryException
     *         if the find query failed.
     *
     * @param ezcSearchQuery $query
     *
     * @return ezcSearchResult
     */
    public function find( ezcSearchQuery $query )
    {
        return $this->handler->find( $query );
    }

    /**
     * Returns a search query for the given document type $type.
     *
     * The query is initialized to fetch all properties.
     *
     * Example:
     * <code>
     * $q = $session->createFindQuery( 'Person' );
     * $allPersons = $session->find( $q, 'Person' );
     * </code>
     *
     * @throws ezcSearchException
     *         if there is no such document type.
     *
     * @param string $type
     *
     * @return ezcSearchFindQuery
     */
    public function createFindQuery( $type )
    {
        $def = $this->definitionManager->fetchDefinition( $type );

        /* We add the ezcsearch_type field to the definition automatically here, but we delete it as well */
        $def->fields['ezcsearch_type'] = new ezcSearchDefinitionDocumentField( 'ezcsearch_type', ezcSearchDocumentDefinition::STRING );
        $res = $this->handler->createFindQuery( $type, $def );
        unset( $def->fields['ezcsearch_type'] );

        return $res;
    }

    /**
     * Starts a transaction for indexing.
     *
     * When using a transaction, the amount of processing that the search
     * backend does decreases, increasing indexing performance. Without this,
     * the component sends a commit after every document that is indexed.
     * Transactions can be nested, when commit() is called the same number of
     * times as beginTransaction(), the component sends a commit.
     */
    public function beginTransaction()
    {
        $this->handler->beginTransaction();
    }

    /**
     * Ends a transaction and calls commit.
     *
     * @throws ezcSearchTransactionException if no transaction is active.
     */
    public function commit()
    {
        $this->handler->commit();
    }

    /**
     * Indexes the new document $document to the search index.
     *
     * @throws ezcSearchException if $document
     *         is not of a valid document type.
     * @throws ezcSearchException
     *         if it was not possible to generate a unique identifier for the
     *         new object.
     * @throws ezcSearchException
     *         if the indexing failed.
     *
     * @param object $document
     */
    public function index( $document )
    {
        $class = get_class( $document );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $document->getState();
        if ( $state[$def->idProperty] == null )
        {
            $state[$def->idProperty] = uniqid();
            $document->setState( array( $def->idProperty => $state[$def->idProperty] ) );
        }
        $this->verifyState( $def, $state );
        $this->handler->index( $def, $state );
    }

    /**
     * Checks whether the state contains all the elements from the definition.
     *
     * @param ezcSearchDocumentDefinition $def
     * @param array(string=>mixed) $state
     *
     * @throws ezcSearchIncompleteStateException if the state is not complete
     */
    private function verifyState( ezcSearchDocumentDefinition $def, array $state )
    {
        foreach ( $def->fields as $field )
        {
            if ( !array_key_exists( $field->field, $state ) )
            {
                throw new ezcSearchIncompleteStateException( $field->field );
            }
        }
    }

    /**
     * Indexes a new document after removing the old one first.
     *
     * @throws ezcSearchDefinitionNotFoundException if $document is not of a valid document type.
     * @throws ezcSearchDocumentNotAvailableException if $document is not stored in the database already.
     * @param object $document
     * @return void
     */
    public function update( $document )
    {
        $type = get_class( $document );
        $def = $this->definitionManager->fetchDefinition( $type );
        $idProperty = $def->idProperty;
        $this->deleteById( $document->$idProperty, $type );
        return $this->index( $document );
    }

    /**
     * Deletes the document $document from the index.
     *
     * @throws ezcSearchDefinitionNotFoundxception
     *         if the object is not recognized as valid document type.
     * @throws ezcSearchDocumentNotAvailableException if $document is not stored in the database already
     * @throws ezcSearchQueryException
     *         if the object could not be deleted.
     *
     * @param ezcSearchDeleteQuery $query
     */
    public function delete( ezcSearchDeleteQuery $query )
    {
        $this->handler->delete( $query );
    }

    /**
     * Returns a delete query for the given document type $type.
     *
     * Example:
     * <code>
     * $q = $session->createDeleteQuery( 'Person' );
     * $q->where( $q->gt( 'age', $q->bindValue( 15 ) ) );
     * $session->delete( $q );
     * </code>
     *
     * @throws ezcSearchException
     *         if there is no such document type.
     *
     * @param string $type
     *
     * @return ezcSearchDeleteQuery
     */
    public function createDeleteQuery( $type )
    {
        $def = $this->definitionManager->fetchDefinition( $type );

        /* We add the ezcsearch_type field to the definition automatically
         * here, but we delete it as well */
        $def->fields['ezcsearch_type'] = new ezcSearchDefinitionDocumentField( 'ezcsearch_type', ezcSearchDocumentDefinition::STRING );
        $res = $this->handler->createDeleteQuery( $type, $def );
        unset( $def->fields['ezcsearch_type'] );

        return $res;
    }

    /**
     * Deletes a document by the document's $id
     *
     * @throws ezcSearchException
     *         if there is no such document type.
     *
     * @param mixed $id
     * @param string $type
     */
    public function deleteById( $id, $type )
    {
        $def = $this->definitionManager->fetchDefinition( $type );
        return $this->handler->deleteById( $id, $def );
    }

    /**
     * Find a document by its ID.
     *
     * @throws ezcSearchException
     *         if there is no such document type.
     *
     * @param mixed $id
     * @param string $type
     * @return ezcSearchResult
     */
    public function findById( $id, $type )
    {
        $def = $this->definitionManager->fetchDefinition( $type );
        return $this->handler->findById( $id, $def );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBasePropertyPermissionException if a read-only property is
     *         tried to be modified.
     *
     * @param string $name
     * @param mixed $value
     *
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'definitionManager':
            case 'handler':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                throw new ezcBasePropertyNotFoundException( $name );
                break;
        }

    }

    /**
     * Property get access.
     *
     * Simply returns a given property.
     * 
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
     *
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) === true )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Returns if a property exists.
     *
     * Returns true if the property exists in the {@link $properties} array
     * (even if it is null) and false otherwise. 
     *
     * @param string $propertyName Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}
?>
