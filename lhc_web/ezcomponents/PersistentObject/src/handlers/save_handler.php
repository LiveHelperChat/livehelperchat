<?php
/**
 * File containing the ezcPersistentSaveHandler class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Helper class for ezcPersistentSession to handle object saving.
 *
 * An instance of this class is used internally in {@link ezcPersistentSession}
 * and takes care for saving and updating objects.
 * 
 * @package PersistentObject
 * @version 1.7.1
 * @access private
 */
class ezcPersistentSaveHandler extends ezcPersistentSessionHandler
{
    /**
     * Registry for {@link ezcPersistentIdentifierGenerator} objects.
     *
     * Caches ID generators so that of every generator class only 1 object
     * exists.
     * 
     * @var array(string=>ezcPersistentIdentifierGenerator)
     */
    private $idGeneratorRegistry = array();

    /**
     * Saves a new persistent $object to the database using an INSERT query.
     *
     * The correct ID is set to $object after it has been saved, as described
     * in its {@link ezcPersistentObjectDefinition}.
     *
     * @throws ezcPersistentObjectException if $object
     *         is not of a valid persistent object type.
     * @throws ezcPersistentObjectException if $object
     *         is already stored to the database.
     * @throws ezcPersistentObjectException
     *         if it was not possible to generate a unique identifier for the
     *         new object.
     * @throws ezcPersistentObjectException
     *         if the INSERT query failed.
     *
     * @param object $object
     */
    public function save( $object )
    {
        $this->saveInternal( $object );
    }

    /**
     * Saves the persistent $object to the database using an UPDATE query.
     *
     * The object needs to have already a valid ID als described in its {@link
     * ezcPersistentObjectDefinition}.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectNotPersistentException
     *         if $object is not stored in the database already.
     * @throws ezcPersistentQueryException
     *         if the UPDATE query fails.
     *
     * @param object $object
     */
    public function update( $object, $updateIgnoreColumns = array()  )
    {
        $this->updateInternal( $object, true, $updateIgnoreColumns );
    }

    /**
     * Saves or updates the persistent object $object to the database.
     *
     * If the object is a new object an INSERT INTO query will be executed. If
     * the object is persistent already it will be updated with an UPDATE
     * query.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if the definition of the persistent object could not be loaded.
     * @throws ezcPersistentObjectException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectException
     *         if any of the definition requirements are not met.
     * @throws ezcPersistentObjectException
     *         if the insert or update query failed.
     * @param object $object
     * @return void
     */
    public function saveOrUpdate( $object, $updateIgnoreColumns = array() )
    {
        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $this->session->getObjectState( $object );

        $idGenerator = $this->getIdGenerator( $def );

        if ( !$idGenerator->checkPersistence( $def, $this->database, $state ) )
        {
            $this->saveInternal( $object, false, $idGenerator );
        }
        else
        {
            $this->updateInternal( $object, false );
        }
    }

    /**
     * Create a relation between $object and $relatedObject.
     *
     * This method is used to create a relation between the given source
     * $object and the desired $relatedObject. The related object is not stored
     * in the database automatically, only the desired properties are set. An
     * exception is {@ezcPersistentManyToManyRelation}s, where the relation
     * record is stored automatically and there is no need to store
     * $relatedObject explicitly after establishing the relation.
     *
     * If multiple relations are defined between the class of $object and the
     * one of $relatedObject, the $relationName is mandatory.
     *
     * @param object $object
     * @param object $relatedObject
     * @param string $relationName
     *
     * @throws ezcPersistentRelationOperationNotSupportedException
     *         if a relation to create is marked as "reverse" {@link
     *         ezcPersistentRelation->reverse}.
     * @throws ezcPersistentRelationNotFoundException
     *         if the deisred relation is not defined.
     */
    public function addRelatedObject( $object, $relatedObject, $relationName = null )
    {
        $class        = get_class( $object );
        $relatedClass = get_class( $relatedObject );
        $def          = $this->definitionManager->fetchDefinition( $class );

        $objectState        = $this->session->getObjectState( $object );
        $relatedObjectState = $this->session->getObjectState( $relatedObject );

        // Sanity check
        if ( !isset( $def->relations[$relatedClass] ) )
        {
            throw new ezcPersistentRelationNotFoundException(
                $class,
                $relatedClass
            );
        }

        $relation = $def->relations[$relatedClass];
        
        // New multi-relations for a single class
        if ( $relation instanceof ezcPersistentRelationCollection )
        {
            if ( $relationName === null )
            {
                throw new ezcPersistentUndeterministicRelationException( $relatedClass );
            }
            if ( !isset( $relation[$relationName] ) )
            {
                throw new ezcPersistentRelationNotFoundException(
                    $class,
                    $relatedClass,
                    $relationName
                );
            }
            $relation = $relation[$relationName];
        }

        // Another sanity check
        if ( isset( $relation->reverse ) && $relation->reverse )
        {
            throw new ezcPersistentRelationOperationNotSupportedException(
                $class,
                $relatedClass,
                __FUNCTION__,
                "Relation is a reverse relation."
            );
        }

        $relatedDef = $this->definitionManager->fetchDefinition( $relatedClass );

        switch ( get_class( $relation ) )
        {
            case "ezcPersistentOneToManyRelation":
            // Not needed, already caught by sanity checks:
            // case "ezcPersistentManyToOneRelation":
            case "ezcPersistentOneToOneRelation":
                foreach ( $relation->columnMap as $map )
                {
                    $relatedObjectState[
                        $relatedDef->columns[$map->destinationColumn]->propertyName
                    ] = $objectState[
                        $def->columns[$map->sourceColumn]->propertyName
                    ];
                }
                $relatedObject->setState( $relatedObjectState );
                break;
            case "ezcPersistentManyToManyRelation":
                $this->insertRelationRecord(
                    $relation,
                    $def,
                    $relatedDef,
                    $objectState,
                    $relatedObjectState
                );
                break;
        }
    }

    /**
     * Returns an update query for the given persistent object $class.
     *
     * The query is initialized to update the correct table and
     * it is only neccessary to set the correct values.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if there is no such persistent class.
     *
     * @param string $class
     *
     * @return ezcQueryUpdate
     */
    public function createUpdateQuery( $class )
    {
        $def = $this->definitionManager->fetchDefinition( $class );

        $q = $this->database->createUpdateQuery();
        $q->setAliases( $this->session->generateAliasMap( $def, false ) );
        $q->update( $this->database->quoteIdentifier( $def->table ) );

        return $q;
    }

    /**
     * Updates persistent objects using the query $query.
     *
     * The $query should be created using createUpdateQuery().
     *
     * Currently this method only executes the provided query. Future
     * releases PersistentSession may introduce caching of persistent objects.
     * When caching is introduced it will be required to use this method to run
     * cusom delete queries. To avoid being incompatible with future releases it is
     * advisable to always use this method when running custom delete queries on
     * persistent objects.
     *
     * @throws ezcPersistentQueryException
     *         if the update query failed.
     *
     * @param ezcQueryUpdate $query
     */
    public function updateFromQuery( ezcQueryUpdate $query )
    {
        $this->session->performQuery( $query, true );
    }

    /**
     * Saves the new persistent object $object to the database using an INSERT INTO query.
     *
     * If $doPersistenceCheck is set this function will check if the object is persistent before
     * saving. If not, the check is omitted. The correct ID is set to $object.
     *
     * @throws ezcPersistentObjectException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectException
     *         if $object is already stored to the database.
     * @throws ezcPersistentObjectException
     *         if it was not possible to generate a unique identifier for the
     *         new object.
     * @throws ezcPersistentObjectException
     *         if the INSERT query failed.
     *
     * @param object $object
     * @param bool $doPersistenceCheck
     * @param ezcPersistentIdentifierGenerator $idGenerator
     */
    private function saveInternal(
        $object,
        $doPersistenceCheck = true,
        ezcPersistentIdentifierGenerator $idGenerator = null 
    )
    {
        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $this->session->getObjectState( $object );
        $castedState = $this->filterAndCastState(
            $state,
            $def
        );
        $idValue = $castedState[$def->idProperty->propertyName];

        $idGenerator = $this->getIdGenerator( $def );

        if ( $doPersistenceCheck == true
             && $idGenerator->checkPersistence( $def, $this->database, $castedState )
        )
        {
            throw new ezcPersistentObjectAlreadyPersistentException( $class );
        }

        // Set up and execute the query.
        $q = $this->buildInsertQuery($def, $castedState);

        // Atomic operation
        $this->database->beginTransaction();
        
        // Let presave id generator do its work.
        $idGenerator->preSave( $def, $this->database, $q );

        // Execute the insert query
        try
        {
            $this->session->performQuery( $q );
        }
        catch ( Exception $e )
        {
            $this->database->rollback();
            throw $e;
        }

        // Fetch the newly created ID, and set it to the objects ID property.
        $id = $idGenerator->postSave( $def, $this->database );
        if ( $id === null )
        {
            // Something must have went wrong, no ID generated.
            $this->database->rollback();
            throw new ezcPersistentIdentifierGenerationException( $class );
        }

        // Everything seems to be fine, lets commit the queries to the database
        // and update the object with its newly created ID.
        $this->database->commit();
        
        $state[$def->idProperty->propertyName] = $id;
        $object->setState( $state );
    }

    /**
     * Saves the new persistent $object to the database using an UPDATE query.
     *
     * If $doPersistenceCheck is set this function will check if the object is
     * persistent before saving. If not, the check is omitted.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectNotPersistentException
     *         if $object is not stored in the database already.
     * @throws ezcPersistentQueryException
     *         if the UPDATE query failed.
     *
     * @param object $object
     * @param bool $doPersistenceCheck
     */
    private function updateInternal( $object, $doPersistenceCheck = true, $updateIgnoreColumns = array() )
    {
        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $this->filterAndCastState(
            $this->session->getObjectState( $object ),
            $def
        );
        $idValue = $state[$def->idProperty->propertyName];

        $idGenerator = $this->getIdGenerator( $def );

        if ( $doPersistenceCheck == true
             && !$idGenerator->checkPersistence( $def, $this->database, $state )
        )
        {
            throw new ezcPersistentObjectNotPersistentException( $class );
        }

        //print_r($updateIgnoreColumns);
       // print_r($state);
       
        foreach ($updateIgnoreColumns as $column) {
        	unset($state[$column]);
        }        
        
        // Set up and execute the query
        $q = $this->buildUpdateQuery($def, $state);

        $this->session->performQuery( $q, true );
    }

    /**
     * Returns the ID generator object defined in $def.
     *
     * Checks {@link $idGeneratorRegistry} for a generator object of the {@link
     * ezcPersistentIdentifierGenerator} class defined in $def. If a
     * corresponding object does not exist, it is created and stored. The
     * fitting generator object is then returned.
     * 
     * @param ezcPersistentObjectDefinition $def 
     * @return ezcPersistentIdentifierGenerator
     */
    private function getIdGenerator( ezcPersistentObjectDefinition $def )
    {
        $genClass = $def->idProperty->generator->class;
        if ( !isset( $this->idGeneratorRegistry[$genClass] ) )
        {
            $idGenerator = new $genClass;
            if ( !( $idGenerator instanceof ezcPersistentIdentifierGenerator ) )
            {
                throw new ezcPersistentIdentifierGenerationException(
                    $def->class,
                    "Could not initialize identifier generator: {$genClass}."
                );
            }
            $this->idGeneratorRegistry[$genClass] = $idGenerator;
        }
        return $this->idGeneratorRegistry[$genClass];
    }

    /**
     * Returns insert query based on a definition and a state.
     *
     * Creates and returns an instance of {@link ezcQueryInsert} and binds the
     * values stored in $state as defined in $def to the query.
     *
     * @param  ezcPersistentObjectDefinition $def
     * @param  array $state
     * @return ezcQueryInsert
     */
    private function buildInsertQuery( ezcPersistentObjectDefinition $def, array $state )
    {
        $q = $this->database->createInsertQuery();
        $q->insertInto( $this->database->quoteIdentifier( $def->table ) );

        $this->bindNonIdPropertyValuesToQuery($q, $def, $state);

        return $q;
    }

    /**
     * Bind all non-id properties to the given query $q.
     *
     * Binds all property values contained in $state to the given query $q, as
     * defined by $def.
     * 
     * @param ezcQueryUpdate|ezcQueryInsert $q
     * @param ezcPersistentObjectDefinition $def
     * @param array $state
     * @return ezcQueryUpdate|ezcQueryInsert
     */
    private function bindNonIdPropertyValuesToQuery( $q, ezcPersistentObjectDefinition $def, array $state )
    {
        foreach ( $state as $name => $value )
        {
            if ( $name !== $def->idProperty->propertyName )
            {
                // Set each of the properties.
                $q->set(
                    $this->database->quoteIdentifier(
                        $def->properties[$name]->columnName
                    ),
                    $q->bindValue( $value, null, $def->properties[$name]->databaseType )
                );
            }
        }
    }

    /**
     * Returns an Update query given a $def and a $state.
     *
     * Creates and returns an {@link ezcQueryUpdate} for the object defined by
     * $def, using all of the property values contained in $state.
     *
     * @param ezcPersistentObjectDefinition $def
     * @param array $state
     * @return ezcQueryUpdate
     */
    private function buildUpdateQuery( ezcPersistentObjectDefinition $def, array $state )
    {
        // Set up and execute the query
        $q = $this->database->createUpdateQuery();
        $q->update( $this->database->quoteIdentifier( $def->table ) );

        $this->bindNonIdPropertyValuesToQuery( $q, $def, $state );
        $this->buildUpdateWhere( $q, $def, $state );

        return $q;
    }

    /**
     * Creates WHERE clause for the given update query $q.
     *
     * Creates the WHERE clause to update the object defined by $def with the
     * property values given in $state into the update query $q.
     *
     * @param ezcQueryUpdate $q
     * @param ezcPersistentObjectDefinition $def
     * @param array $state
     */
    private function buildUpdateWhere( $q, ezcPersistentObjectDefinition $def, array $state )
    {
        $idValue = $state[$def->idProperty->propertyName];

        $q->where(
            $q->expr->eq(
                $this->database->quoteIdentifier(
                    $def->idProperty->columnName
                ),
                $q->bindValue( $idValue, null, $def->idProperty->databaseType )
            )
        );
    }

    /**
     * Filters out all properties not in the definition and casts the
     * values to native PHP types.
     *
     * @param array(string=>string) $state
     * @param ezcPersistentObjectDefinition $def
     * @return array(string=>mixed)
     */
    private function filterAndCastState( array $state, ezcPersistentObjectDefinition $def )
    {
        $typedState = array();
        foreach ( $state as $name => $value )
        {
            $type = null;
            if ( $name === $def->idProperty->propertyName )
            {
                $type = $def->idProperty->propertyType;
                // ID property has no conversion.
                $conv = null;
            }
            else
            {
                if ( !array_key_exists( $name, $def->properties ) )
                {
                    // Unknown property
                    continue;
                }
                $type = $def->properties[$name]->propertyType;
                $conv = $def->properties[$name]->converter;
            }

            // First convert back from complex type.
            if ( !is_null( $conv ) )
            {
                $value = $conv->toDatabase( $value );
            }

            if ( !is_null( $value ) )
            {
                // Then cast simple type.
                switch ( $type )
                {
                    case ezcPersistentObjectProperty::PHP_TYPE_INT:
                        $value = (int) $value;
                        break;
                    case ezcPersistentObjectProperty::PHP_TYPE_FLOAT:
                        $value = (float) $value;
                        break;
                    case ezcPersistentObjectProperty::PHP_TYPE_BOOL:
                        $value = (bool) $value;
                        break;
                    case ezcPersistentObjectProperty::PHP_TYPE_STRING:
                        $value = (string) $value;
                        break;
                }
            }

            $typedState[$name] = $value;
        }
        return $typedState;
    }

    /**
     * Inserts the relation record for a many-to-many relation.
     * 
     * @param ezcPersistentManyToManyRelation $relation 
     * @param ezcPersistentObjectDefinition $def
     * @param ezcPersistentObjectDefinition $relatedDef
     * @param array $objectState 
     * @param array $relatedObjectState 
     */
    private function insertRelationRecord(
        ezcPersistentManyToManyRelation $relation,
        ezcPersistentObjectDefinition $def,
        ezcPersistentObjectDefinition $relatedDef,
        array $objectState,
        array $relatedObjectState
    )
    {
        $q = $this->database->createInsertQuery();
        $q->insertInto(
            $this->database->quoteIdentifier( $relation->relationTable )
        );
        $insertColumns = array();
        foreach ( $relation->columnMap as $map )
        {
            if ( !in_array( $map->relationSourceColumn, $insertColumns ) )
            {
                $q->set(
                    $this->database->quoteIdentifier(
                        $map->relationSourceColumn
                    ),
                    $q->bindValue(
                        $objectState[
                            $def->columns[$map->sourceColumn]->propertyName
                        ],
                        null,
                        $def->columns[$map->sourceColumn]->databaseType
                    )
                );
                $insertColumns[] = $map->relationSourceColumn;
            }
            if ( !in_array( $map->relationDestinationColumn, $insertColumns ) )
            {
                $q->set(
                    $this->database->quoteIdentifier(
                        $map->relationDestinationColumn
                    ),
                    $q->bindValue(
                        $relatedObjectState[
                            $relatedDef->columns[$map->destinationColumn]->propertyName
                        ],
                        null,
                        $relatedDef->columns[$map->destinationColumn]->databaseType
                    )
                );
                $insertColumns[] = $map->relationDestinationColumn;
            }
        }
        $this->session->performQuery( $q, true );
    }
}

?>
