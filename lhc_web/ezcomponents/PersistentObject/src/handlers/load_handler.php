<?php
/**
 * File containing the ezcPersistentLoadHandler class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Helper class for ezcPersistentSession to handle object loading.
 *
 * An instance of this class is used internally in {@link ezcPersistentSession}
 * and takes care for loading and finding objects.
 * 
 * @package PersistentObject
 * @version 1.7.1
 * @access private
 */
class ezcPersistentLoadHandler extends ezcPersistentSessionHandler
{
    /**
     * Returns the persistent object of class $class with id $id.
     *
     * @throws ezcPersistentObjectNotFoundException
     *         if the object is not available.
     * @throws ezcPersistentObjectException
     *         if there is no such persistent class.
     *
     * @param string $class
     * @param int $id
     *
     * @return object
     */
    public function load( $class, $id )
    {
        $def    = $this->definitionManager->fetchDefinition( $class );
        $object = new $def->class;

        $this->loadIntoObject( $object, $id );

        return $object;
    }
    
    public function loadAndLock( $class, $id )
    {
        $def    = $this->definitionManager->fetchDefinition( $class );
        $object = new $def->class;

        $this->loadIntoObject( $object, $id, true);

        return $object;
    }
    
    /**
     * Returns the persistent object of class $class with id $id.
     *
     * This method is equivalent to {@link load()} except that it returns null
     * instead of throwing an exception if the object does not exist.
     *
     * @param string $class
     * @param int $id
     *
     * @return object|null
     * @apichange This method will catch only exceptions which are related to 
     *            the loading itself in future major releases.
     */
    public function loadIfExists( $class, $id )
    {
        $result = null;
        try
        {
            $result = $this->load( $class, $id );
        }
        catch ( ezcPersistentObjectException $e )
        {
            // Eat, we return null on error.
        }
        return $result;
    }

    /**
     * Loads the persistent object with the id $id into the object $object.
     *
     * The class of the persistent object to load is determined by the class
     * of $object.
     *
     * @throws ezcPersistentObjectNotFoundException
     *         if the object is not available.
     * @throws ezcPersistentDefinitionNotFoundException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     *
     * @param object $object
     * @param int $id
     */
    public function loadIntoObject( $object, $id, $lock = false )
    {
        $def = $this->definitionManager->fetchDefinition(
            get_class( $object ) 
        );

        // Prepare query.
        $q = $this->database->createSelectQuery();
        $q->select(
            $this->session->getColumnsFromDefinition( $def )
        )->from(
            $this->database->quoteIdentifier( $def->table )
        )->where(
            $q->expr->eq(
                $this->database->quoteIdentifier( $def->idProperty->columnName ),
                $q->bindValue( $id, null, $def->idProperty->databaseType )
            )
        );

        // This is lock query
        if ($lock === true) {
            $q->doLock();
        }
                
        // Execute and fetch rows.
        $stmt = $this->session->performQuery( $q );
        $row  = $stmt->fetch( PDO::FETCH_ASSOC );
        $stmt->closeCursor();

        // Convert result into object.
        if ( $row !== false )
        {
            // We could check if there was more than one result here
            // but we don't because of the overhead and since the Persistent
            // Object would be faulty by design in that case and the user would have
            // to execute custom code to get into an invalid state.
            try
            {
                $state = ezcPersistentStateTransformer::rowToStateArray(
                    $row,
                    $def
                );
            }
            catch ( Exception $e )
            {
                throw new ezcPersistentObjectException(
                    "The row data could not be correctly converted to set data.",
                    "Most probably there is something wrong with a custom rowToStateArray implementation"
                );
            }
            $object->setState( $state );
        }
        else
        {
            $class = get_class( $object );
            throw new ezcPersistentObjectNotFoundException( $class, $id );
        }
    }

    /**
     * Syncronizes the contents of $object with those in the database.
     *
     * Note that calling this method is equavalent with calling {@link
     * loadIntoObject()} on $object with the id of $object. Any changes made
     * to $object prior to calling refresh() will be discarded.
     *
     * @throws ezcPersistentObjectException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectException
     *         if $object is not persistent already.
     * @throws ezcPersistentObjectException
     *         if the select query failed.
     *
     * @param object $object
     */
    public function refresh( $object )
    {
        $class   = get_class( $object );
        $def     = $this->definitionManager->fetchDefinition( $class );
        $state   = $this->session->getObjectState( $object );
        $idValue = $state[$def->idProperty->propertyName];
        
        if ( $idValue !== null )
        {
            $this->loadIntoObject( $object, $idValue );
        }
        else
        {
            throw new ezcPersistentObjectNotPersistentException( $class );
        }
    }

    /**
     * Returns the result of the query $query as a list of objects.
     *
     * Returns the persistent objects found for $class using the submitted
     * $query. $query should be created using {@link createFindQuery()} to
     * ensure correct alias mappings and can be manipulated as needed.
     *
     * Example:
     * <code>
     * $q = $session->createFindQuery( 'Person' );
     * $allPersons = $session->find( $q, 'Person' );
     * </code>
     *
     * If you are retrieving large result set, consider using {@link
     * findIterator()} instead.
     *
     * Example:
     * <code>
     * $q = $session->createFindQuery( 'Person' );
     * $objects = $session->findIterator( $q, 'Person' );
     *
     * foreach( $objects as $object )
     * {
     *     // ...
     * }
     * </code>
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if there is no such persistent class.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     * @throws ezcBaseValueException
     *         if $query parameter is not an instance of ezcPersistentFindQuery
     *         or ezcQuerySelect. Or if $class is missing if you use
     *         ezcQuerySelect.
     *
     * @param ezcPersistentFindQuery|ezcQuerySelect $query
     * @param string $class
     *
     * @return array(object($class))
     *
     * @apichange This method will only accept an instance of
     *            ezcPersistentFindQuery as the $query parameter in future
     *            major releases. The $class parameter will be removed.
     */
    public function find( $query, $class = null )
    {
        // Sanity checks
        if ( !is_object( $query )
             || ( !( $query instanceof ezcPersistentFindQuery )
                  && !( $query instanceof ezcQuerySelect )
                )
           )
        {
            throw new ezcBaseValueException(
                'query',
                $query,
                'ezcPersistentFindQuery (or ezcQuerySelect)'
            );
        }
        if ( $query instanceof ezcQuerySelect && $class === null )
        {
            throw new ezcBaseValueException(
                'class',
                $class,
                'must be present, if ezcQuerySelect is used for $query'
            );
        }

        // Extract class name and select query form parameter
        if ( $query instanceof ezcPersistentFindQuery )
        {
            $class = $query->className;
            $query = $query->query;
        }

        $def = $this->definitionManager->fetchDefinition( $class );

        $rows = $this->session->performQuery( $query )
            ->fetchAll( PDO::FETCH_ASSOC );

        // Convert all the rows to states and then to objects.
        $result = array();
        foreach ( $rows as $row )
        {
            $object = new $def->class;
            $object->setState(
                ezcPersistentStateTransformer::rowToStateArray( $row, $def )
            );
            $result[$row[$def->idProperty->resultColumnName]] = $object;
        }
        return $result;
    }

    /**
     * Returns the result of $query for the $class as an iterator.
     *
     * This method is similar to {@link find()} but returns an {@link
     * ezcPersistentFindIterator} instead of an array of objects. This is
     * useful if you are going to loop over the objects and just need them one
     * at the time.  Because you only instantiate one object it is faster than
     * {@link find()}. In addition, only 1 record is retrieved from the
     * database in each iteration, which may reduce the data transfered between
     * the database and PHP, if you iterate only through a small subset of the
     * affected records.
     *
     * Note that if you do not loop over the complete result set you must call
     * {@link ezcPersistentFindIterator::flush()} before issuing another query.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if there is no such persistent class.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     * @throws ezcBaseValueException
     *         if $query parameter is not an instance of ezcPersistentFindQuery
     *         or ezcQuerySelect. Or if $class is missing if you use
     *         ezcQuerySelect.
     *
     * @param ezcPersistentFindQuery|ezcQuerySelect $query
     * @param string $class
     *
     * @return ezcPersistentFindIterator
     * @apichange This method will only accept an instance of
     *            ezcPersistentFindQuery as the $query parameter in future
     *            major releases. The $class parameter will be removed.
     */
    public function findIterator( $query, $class = null )
    {
        // Sanity checks
        if ( !is_object( $query )
             || ( !( $query instanceof ezcPersistentFindQuery )
                  && !( $query instanceof ezcQuerySelect )
                )
           )
        {
            throw new ezcBaseValueException(
                'query',
                $query,
                'ezcPersistentFindQuery (or ezcQuerySelect)'
            );
        }
        if ( $query instanceof ezcQuerySelect && $class === null )
        {
            throw new ezcBaseValueException(
                'class',
                $class,
                'must be present, if ezcQuerySelect is used for $query'
            );
        }

        // Extract class name and select query form parameter
        if ( $query instanceof ezcPersistentFindQuery )
        {
            $class = $query->className;
            $query = $query->query;
        }

        $def  = $this->definitionManager->fetchDefinition( $class );
        $stmt = $this->session->performQuery( $query );
        return new ezcPersistentFindIterator( $stmt, $def );
    }

    /**
     * Returns the related objects of a given $relatedClass for an $object.
     *
     * This method returns the related objects of type $relatedClass for the
     * given $object. This method (in contrast to {@link getRelatedObject()})
     * always returns an array of found objects, no matter if only 1 object
     * was found (e.g. {@link ezcPersistentManyToOneRelation}), none or several
     * ({@link ezcPersistentManyToManyRelation}).
     *
     * Example:
     * <code>
     * $person = $session->load( "Person", 1 );
     * $relatedAddresses = $session->getRelatedObjects( $person, "Address" );
     * echo "Number of addresses found: " . count( $relatedAddresses );
     * </code>
     *
     * Relations that should preferably be used with this method are:
     * <ul>
     * <li>{@link ezcPersistentOneToManyRelation}</li>
     * <li>{@link ezcPersistentManyToManyRelation}</li>
     * </ul>
     * For other relation types {@link getRelatedObject()} is recommended.
     *
     * If multiple relations are defined for the $relatedClass (using {@link
     * ezcPersistentRelationCollection}), the parameter $relationName becomes
     * mandatory to determine which relation definition to use. For normal
     * relations, this parameter is silently ignored.
     *
     * @param object $object
     * @param string $relatedClass
     * @param string $relationName
     *
     * @return array(int=>object($relatedClass))
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function getRelatedObjects( $object, $relatedClass, $relationName = null )
    {
        $query = $this->createRelationFindQuery( $object, $relatedClass, $relationName );
        return $this->find( $query, $relatedClass );
    }

    /**
     * Returns the related object of a given $relatedClass for an $object.
     *
     * This method returns the related object of type $relatedClass for the
     * object $object. This method (in contrast to {@link getRelatedObjects()})
     * always returns a single result object, no matter if more related objects
     * could be found (e.g. {@link ezcPersistentOneToManyRelation}). If no
     * related object is found, an exception is thrown, while {@link
     * getRelatedObjects()} just returns an empty array in this case.
     *
     * Example:
     * <code>
     * $person = $session->load( "Person", 1 );
     * $relatedAddress = $session->getRelatedObject( $person, "Address" );
     * echo "Address of this person: " . $relatedAddress->__toString();
     * </code>
     *
     * Relations that should preferably be used with this method are:
     * <ul>
     * <li>{@link ezcPersistentManyToOneRelation}</ li>
     * <li>{@link ezcPersistentOneToOneRelation}</li>
     * </ul>
     * For other relation types {@link getRelatedObjects()} is recommended.
     *
     * If multiple relations are defined for the $relatedClass (using {@link
     * ezcPersistentRelationCollection}), the parameter $relationName becomes
     * mandatory to determine which relation definition to use. For normal
     * relations, this parameter is silently ignored.
     *
     * @param object $object
     * @param string $relatedClass
     * @param string $relationName
     *
     * @return object($relatedClass)
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function getRelatedObject( $object, $relatedClass, $relationName = null )
    {
        $query = $this->createRelationFindQuery( $object, $relatedClass, $relationName );
        // This method only needs to return 1 object
        $query->limit( 1 );

        $resArr = $this->find( $query, $relatedClass );
        if ( sizeof( $resArr ) < 1 )
        {
            throw new ezcPersistentRelatedObjectNotFoundException(
                $object,
                $relatedClass
            );
        }
        return reset( $resArr );
    }

    /**
     * Returns a select query for the given persistent object $class.
     *
     * The query is initialized to fetch all columns from the correct table and
     * has correct alias mappings between columns and property names of the
     * persistent $class.
     *
     * Example:
     * <code>
     * $q = $session->createFindQuery( 'Person' );
     * $allPersons = $session->find( $q, 'Person' );
     * </code>
     *
     * @throws ezcPersistentObjectException
     *         if there is no such persistent class.
     *
     * @param string $class
     *
     * @return ezcPersistentFindQuery
     */
    public function createFindQuery( $class, $ignoreColumns = array() )
    {
        $def = $this->definitionManager->fetchDefinition( $class );

        // Init query
        $q = $this->database->createSelectQuery();
        $q->setAliases( $this->session->generateAliasMap( $def ) );

        $q->select(
            $this->session->getColumnsFromDefinition( $def, true, $ignoreColumns )
        )->from(
            $this->database->quoteIdentifier( $def->table )
        );

        $findQuery = new ezcPersistentFindQuery( $q, $class );

        return $findQuery;
    }

    /**
     * Returns a sub-select for the given $class to be used with $parentQuery.
     *
     * This method creates an {@link ezcPersistentFindQuery} as a {@link 
     * ezcQuerySubSelect} for the given $class. The returned query has already
     * set aliases for the properties of $class, but (in contrast to the query
     * returned by {@link createFindQuery()}) does not have the selection of all
     * properties set. You need to do
     *
     * <code>
     * <?php
     * $subSelect = $session->subSelect( $existingSelectQuery, 'MyClass' );
     * $subSelect->select( 'myField' );
     * ?>
     * </code>
     *
     * manually to select the fields you desire.
     * 
     * @param ezcPersistentFindQuery $parentQuery 
     * @param string $class 
     * @return ezcQuerySubSelect
     */
    public function createSubQuery( ezcPersistentFindQuery $parentQuery, $class )
    {
        $subSelect = $parentQuery->subSelect();

        $def = $this->definitionManager->fetchDefinition( $class );
        $subSelect->setAliases( $this->session->generateAliasMap( $def ) );

        $subSelect->from( $this->database->quoteIdentifier( $def->table ) );

        return $subSelect;
    }

    /**
     * Returns the base query for retrieving related objects.
     *
     * See {@link getRelatedObject()} and {@link getRelatedObjects()}. Can be
     * modified by additional where conditions and simply be used with
     * {@link find()} and the related class name, to retrieve a sub-set of
     * related objects.
     *
     * If multiple relations exist to the same PHP class (defined using a
     * {@link ezcPersistentRelationCollection}), the optional parameter
     * $relationName becomes mandatory to determine the relation to use for
     * fetching objects. If the parameter is not submitted, an exception will
     * be thrown. For normal relations this parameter will be silently ignored.
     *
     * @param object $object
     * @param string $relatedClass
     * @param string $relationName
     *
     * @return ezcPersistentFindQuery
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function createRelationFindQuery( $object, $relatedClass, $relationName = null )
    {
        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );

        if ( !isset( $def->relations[$relatedClass] ) )
        {
            throw new ezcPersistentRelationNotFoundException(
                $class,
                $relatedClass
            );
        }
        $relation    = $def->relations[$relatedClass];

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

        $query       = $this->createFindQuery( $relatedClass );
        $objectState = $this->session->getObjectState( $object );

        switch ( ( $relationClass = get_class( $relation ) ) )
        {
            case "ezcPersistentOneToManyRelation":
            case "ezcPersistentManyToOneRelation":
            case "ezcPersistentOneToOneRelation":
                $this->createSimpleRelationFindQuery( $query, $def, $relation, $objectState );
                break;
            case "ezcPersistentManyToManyRelation":
                $this->createComplexRelationFindQuery( $query, $def, $relation, $objectState );
                break;
            default:
                throw new ezcPersistentRelationInvalidException( $relationClass );
        }
        return $query;
    }

    /**
     * Sets find query value for simple related objects. 
     *
     * Manipulates the find $query for objects related to the object defined in
     * $objectState, defined my the relation $relation. This method is
     * responsile for
     * <ul>
     *     <li>{@link ezcPersistentOneToManyRelation}</li>
     *     <li>{@link ezcPersistentOneToOneRelation}</li>
     *     <li>{@link ezcPersistentManyToOneRelatio}n</li>
     * </ul>
     * for {@link ezcPersistentManyToManyRelation} see {@link
     * createComplexRelationFindQuery()}.
     * 
     * @param ezcPersistentFindQuery $query 
     * @param ezcPersistentObjectDefinition $def
     * @param ezcPersistentRelation $relation 
     * @param array $objectState 
     */
    private function createSimpleRelationFindQuery(
        ezcPersistentFindQuery $query,
        ezcPersistentObjectDefinition $def,
        ezcPersistentRelation $relation,
        array $objectState
    )
    {
        foreach ( $relation->columnMap as $map )
        {
            $query->where(
                $query->expr->eq(
                    $this->database->quoteIdentifier(
                        $map->destinationColumn
                    ),
                    $query->bindValue(
                        $objectState[$def->columns[$map->sourceColumn]->propertyName],
                        null,
                        $def->columns[$map->sourceColumn]->databaseType
                    )
                )
            );
        }
    }

    /**
     * Sets find query value for many-to-many related objects. 
     *
     * Manipulates the find $query for objects related to the object defined in
     * $objectState, defined my the relation $relation.
     * 
     * @param ezcPersistentFindQuery $query 
     * @param ezcPersistentObjectDefinition $def
     * @param ezcPersistentManyToManyRelation $relation 
     * @param array $objectState 
     */
    private function createComplexRelationFindQuery(
        ezcPersistentFindQuery $query,
        ezcPersistentObjectDefinition $def,
        ezcPersistentManyToManyRelation $relation,
        array $objectState
    )
    {
        $db = $this->database;
        $relationTableQuoted = $db->quoteIdentifier( $relation->relationTable );
        // Join with relation table.
        $query->from( $relationTableQuoted );
        foreach ( $relation->columnMap as $map )
        {
            $query->where(
                $query->expr->eq(
                    $relationTableQuoted . "." .  $db->quoteIdentifier( $map->relationSourceColumn ),
                    $query->bindValue(
                        $objectState[$def->columns[$map->sourceColumn]->propertyName],
                        null,
                        $def->columns[$map->sourceColumn]->databaseType
                    )
                ),
                $query->expr->eq(
                    $relationTableQuoted . "." .  $db->quoteIdentifier( $map->relationDestinationColumn ),
                    $db->quoteIdentifier( $relation->destinationTable ) .  "." .  $db->quoteIdentifier( $map->destinationColumn )
                )
            );
        }
    }
}

?>
