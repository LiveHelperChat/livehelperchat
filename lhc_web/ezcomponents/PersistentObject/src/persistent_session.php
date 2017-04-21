<?php
/**
 * File containing the ezcPersistentSession class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcPersistentSession is the main runtime interface for manipulation of persistent objects.
 *
 * Persistent objects can be stored calling save() resulting in an INSERT query. If
 * the object is already persistent you can store it using update() which results in
 * an UPDATE query. If you want to query persistent objects you can use the find methods.
 *
 * @property-read ezcDbHandler $database
 *                The database handler set in the constructor.
 * @property-read ezcPersistentDefinitionManager $definitionManager
 *                The persistent definition manager set in the constructor.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @mainclass
 */
class ezcPersistentSession implements ezcPersistentSessionFoundation
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Constructs a new persistent session that works on the database $db.
     *
     * The $manager provides valid persistent object definitions to the
     * session. The $db will be used to perform all database operations.
     *
     * @param ezcDbHandler $db
     * @param ezcPersistentDefinitionManager $manager
     */
    public function __construct( ezcDbHandler $db, ezcPersistentDefinitionManager $manager )
    {
        $this->properties['database']          = $db;
        $this->properties['definitionManager'] = $manager;
        $this->properties['loadHandler']       = new ezcPersistentLoadHandler( $this );
        $this->properties['saveHandler']       = new ezcPersistentSaveHandler( $this );
        $this->properties['deleteHandler']     = new ezcPersistentDeleteHandler( $this );
    }

    /**
     * Returns the persistent object of class $class with id $id.
     *
     * @throws ezcPersistentObjectException
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
        return $this->loadHandler->load( $class, $id );
    }
    
    /**
     * Returns the persistent object of class $class with id $id. Also locks the record itself.
     *
     * @throws ezcPersistentObjectException
     *         if the object is not available.
     * @throws ezcPersistentObjectException
     *         if there is no such persistent class.
     *
     * @param string $class
     * @param int $id
     *
     * @return object
     */
    public function loadAndLock( $class, $id )
    {
        return $this->loadHandler->loadAndLock( $class, $id );
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
     */
    public function loadIfExists( $class, $id )
    {
        return $this->loadHandler->loadIfExists( $class, $id );
    }

    /**
     * Loads the persistent object with the id $id into the object $object.
     *
     * The class of the persistent object to load is determined by the class
     * of $object.
     *
     * @throws ezcPersistentObjectException
     *         if the object is not available.
     * @throws ezcPersistentDefinitionNotFoundException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     *
     * @param object $object
     * @param int $id
     */
    public function loadIntoObject( $object, $id )
    {
        return $this->loadHandler->loadIntoObject( $object, $id );
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
        return $this->loadHandler->refresh( $object );
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
     * @apichange This method will only accept an instance of
     *            ezcPersistentFindQuery as the $query parameter in future
     *            major releases. The $class parameter will be removed.
     */
    public function find( $query, $class = null )
    {
        return $this->loadHandler->find( $query, $class );
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
        return $this->loadHandler->findIterator( $query, $class );
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
        return $this->loadHandler->getRelatedObjects( $object, $relatedClass, $relationName );
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
     * <li>{@link ezcPersistentManyToOneRelation}</li>
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
        return $this->loadHandler->getRelatedObject( $object, $relatedClass, $relationName );
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
     * @return ezcQuerySelect
     */
    public function createFindQuery( $class, $ignoreColumns = array() )
    {
        return $this->loadHandler->createFindQuery( $class, $ignoreColumns );
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
        return $this->loadHandler->createSubQuery( $parentQuery, $class );
    }

    /**
     * Returns the base query for retrieving related objects.
     *
     * See {@link getRelatedObject()} and {@link getRelatedObjects()}. Can be
     * modified by additional where conditions and simply be used with
     * {@link find()} and the related class name, to retrieve a sub-set of
     * related objects.
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
     * @return ezcPersistentFindQuery
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function createRelationFindQuery( $object, $relatedClass, $relationName = null )
    {
        return $this->loadHandler->createRelationFindQuery( $object, $relatedClass, $relationName );
    }

    /**
     * Saves the new persistent object $object to the database using an INSERT INTO query.
     *
     * The correct ID is set to $object.
     *
     * @throws ezcPersistentObjectException if $object
     *         is not of a valid persistent object type.
     * @throws ezcPersistentObjectException if $object
     *         is already stored to the database.
     * @throws ezcPersistentObjectException
     *         if it was not possible to generate a unique identifier for the
     *         new object.
     * @throws ezcPersistentObjectException
     *         if the insert query failed.
     *
     * @param object $object
     */
    public function save( $object )
    {
        return $this->saveHandler->save( $object );
    }

    /**
     * Saves the new persistent object $object to the database using an UPDATE query.
     *
     * @throws ezcPersistentDefinitionNotFoundException if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectNotPersistentException if $object is not stored in the database already.
     * @throws ezcPersistentQueryException
     * @param object $object
     * @return void
     */
    public function update( $object, $updateIgnoreColumns = array() )
    {
        return $this->saveHandler->update( $object, $updateIgnoreColumns );
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
    public function saveOrUpdate( $object )
    {
        return $this->saveHandler->saveOrUpdate( $object );
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
     * If there are multiple relations defined between the class of $object and
     * $relatedObject (via {@link ezcPersistentRelationCollection}), the
     * $relationName parameter becomes mandatory to determine, which exact
     * relation should be used.
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
        return $this->saveHandler->addRelatedObject( $object, $relatedObject, $relationName );
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
        return $this->saveHandler->createUpdateQuery( $class );
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
        return $this->saveHandler->updateFromQuery( $query );
    }

    /**
     * Deletes the persistent object $object.
     *
     * This method will perform a DELETE query based on the identifier of the
     * persistent object $object. After delete() the ID property of $object
     * will be reset to null. It is possible to {@link save()} $object
     * afterwards.  $object will then be stored with a new ID.
     *
     * If you defined relations for the given object, these will be checked to
     * be defined as cascading. If cascading is configured, the related objects
     * with this relation will be deleted, too.
     *
     * Relations that support cascading are:
     * <ul>
     * <li>{@link ezcPersistenOneToManyRelation}</li>
     * <li>{@link ezcPersistenOneToOne}</li>
     * </ul>
     *
     * @throws ezcPersistentDefinitionNotFoundxception
     *         if $the object is not recognized as a persistent object.
     * @throws ezcPersistentObjectNotPersistentException
     *         if the object is not persistent already.
     * @throws ezcPersistentQueryException
     *         if the object could not be deleted.
     *
     * @param object $object The persistent object to delete.
     */
    public function delete( $object )
    {
        return $this->deleteHandler->delete( $object );
    }

    /**
     * Removes the relation between $object and $relatedObject.
     *
     * This method is used to delete an existing relation between 2 objects.
     * Like {@link addRelatedObject()} this method does not store the related
     * object after removing its relation properties (unset), except for {@link
     * ezcPersistentManyToManyRelation()}s, for which the relation record is
     * deleted from the database.
     *
     * If between the classes of $object and $relatedObject multiple relations
     * are defined using a {@link ezcPersistentRelationCollection}, the
     * $relationName parameter becomes necessary. It defines which exact
     * relation to affect here.
     *
     * @param object $object        Source object of the relation.
     * @param object $relatedObject Related object.
     * @param string $relationName
     *
     * @throws ezcPersistentRelationOperationNotSupportedException
     *         if a relation to create is marked as "reverse".
     * @throws ezcPersistentRelationNotFoundException
     *         if the deisred relation is not defined.
     */
    public function removeRelatedObject( $object, $relatedObject, $relationName = null )
    {
        return $this->deleteHandler->removeRelatedObject( $object, $relatedObject, $relationName );
    }

    /**
     * Deletes persistent objects using the query $query.
     *
     * The $query should be created using {@link createDeleteQuery()}.
     *
     * Currently this method only executes the provided query. Future
     * releases PersistentSession may introduce caching of persistent objects.
     * When caching is introduced it will be required to use this method to run
     * cusom delete queries. To avoid being incompatible with future releases it is
     * advisable to always use this method when running custom delete queries on
     * persistent objects.
     *
     * @throws ezcPersistentQueryException
     *         if the delete query failed.
     *
     * @param ezcQueryDelete $query
     */
    public function deleteFromQuery( ezcQueryDelete $query )
    {
        return $this->deleteHandler->deleteFromQuery( $query );
    }

    /**
     * Returns a delete query for the given persistent object $class.
     *
     * The query is initialized to delete from the correct table and
     * it is only neccessary to set the where clause.
     *
     * Example:
     * <code>
     * $q = $session->createDeleteQuery( 'Person' );
     * $q->where( $q->expr->gt( 'age', $q->bindValue( 15 ) ) );
     * $session->deleteFromQuery( $q );
     * </code>
     *
     * @throws ezcPersistentObjectException
     *         if there is no such persistent class.
     *
     * @param string $class
     *
     * @return ezcQueryDelete
     */
    public function createDeleteQuery( $class )
    {
        return $this->deleteHandler->createDeleteQuery( $class );
    }

    /**
     * Returns if $relatedObject is related to $sourceObject.
     *
     * Checks the relation conditions between $sourceObject and $relatedObject 
     * and returns true, if $relatedObject is related to $sourceObject, 
     * otherwise false. In case multiple relations are defined between the
     * classes of $sourceObject and $relatedObject, the $relationName parameter
     * becomes mandatory. If it is not provided in this case, an {@link 
     * ezcPersistentUndeterministicRelationException} is thrown.
     *
     * Note that checking relations of type {@link 
     * ezcPersistentManyToManyRelation} will issue a database query. Other relations will 
     * not perform this.
     * 
     * @param ezcPersistentObject $sourceObj 
     * @param ezcPersistentObject $relatedObj 
     * @param string $relationName
     * @return bool
     */
    public function isRelated( $sourceObject, $relatedObject, $relationName = null )
    {
        $srcClass = get_class( $sourceObject );
        $relClass = get_class( $relatedObject );

        $srcDef = $this->definitionManager->fetchDefinition( $srcClass );

        if ( !isset( $srcDef->relations[$relClass] ) )
        {
            return false;
        }
        $relationDef = $srcDef->relations[$relClass];

        if ( $relationDef instanceof ezcPersistentRelationCollection )
        {
            if ( $relationName === null )
            {
                throw new ezcPersistentUndeterministicRelationException(
                    $relClass
                );
            }
            if ( !isset( $relationDef[$relationName] ) )
            {
                return false;
            }
            $relationDef = $relationDef[$relationName];
        }

        $relDef = $this->definitionManager->fetchDefinition( $relClass );

        $srcState = $sourceObject->getState();
        $relState = $relatedObject->getState();

        if ( $relationDef instanceof ezcPersistentManyToManyRelation )
        {
            return $this->checkComplexRelation( $srcState, $srcDef, $relState, $relDef, $relationDef );
        }
        else
        {
            return $this->checkSimpleRelation( $srcState, $srcDef, $relState, $relDef, $relationDef );
        }
    }

    /**
     * Returns a hash map between property and column name for the given
     * definition $def.
     *
     * The alias map can be used with the query classes. If $prefixTableName is
     * set to false, only the column names are used as alias targets.
     *
     * @param ezcPersistentObjectDefinition $def Definition.
     * @param bool $prefixTableName
     * @return array(string=>string)
     */
    public function generateAliasMap( ezcPersistentObjectDefinition $def, $prefixTableName = true )
    {
        $table = array();
        $table[$def->idProperty->propertyName] = ( $prefixTableName 
            ? $this->database->quoteIdentifier( $def->table ) . '.' . $this->database->quoteIdentifier( $def->idProperty->columnName )
            : $this->database->quoteIdentifier( $def->idProperty->columnName ) );
        foreach ( $def->properties as $prop )
        {
            $table[$prop->propertyName] = ( $prefixTableName 
                ? $this->database->quoteIdentifier( $def->table ) . '.' . $this->database->quoteIdentifier( $prop->columnName )
                : $this->database->quoteIdentifier( $prop->columnName ) );
        }
        $table[$def->class] = $def->table;
        return $table;
    }

    /**
     * Returns all the columns defined in the persistent object.
     *
     * If $prefixTableName is set to false, raw column names will be used,
     * without prefixed table name.
     *
     * @param ezcPersistentObjectDefinition $def Defintion.
     * @param bool $prefixTableName
     * @return array(int=>string)
     */
    public function getColumnsFromDefinition( ezcPersistentObjectDefinition $def, $prefixTableName = true, $ignoreColumns = array() )
    {
        $hasIgnoreColumns = !empty($ignoreColumns);
        
        $columns = array();
        $columns[] = ( $prefixTableName 
            ? $this->database->quoteIdentifier( $def->table ) . '.' . $this->database->quoteIdentifier( $def->idProperty->columnName )
            : $this->database->quoteIdentifier( $def->idProperty->columnName ) );
        foreach ( $def->properties as $property )
        {
            if ( $hasIgnoreColumns === false || !in_array( $property->columnName, $ignoreColumns ) ) 
            {
                $columns[] = ( $prefixTableName
                    ? $this->database->quoteIdentifier( $def->table ) . '.' . $this->database->quoteIdentifier( $property->columnName )
                    : $this->database->quoteIdentifier( $property->columnName ) );
            }
        }
        return $columns;
    }

    /**
     * Returns the object state.
     *
     * This method wraps around $object->getState() to add optional sanity
     * checks to this call, like a correct return type of getState() and
     * correct keys and values in the returned array.
     * 
     * @param object $object 
     * @return array
     *
     * @access private
     */
    public function getObjectState( $object )
    {
        // Common sanity check.
        if ( !is_array( $state = $object->getState() ) )
        {
            throw new ezcPersistentInvalidObjectStateException(
                $object,
                'Is type ' . gettype( $state ) . ' instead of array.'
            );
        }
        // @todo: Add more optional sanity checks.
        return $state;
    }

    /**
     * Performs the given query.
     *
     * Performs the $query, checks for errors and throws an exception in case.
     * Returns the generated statement object on success. If the $transaction
     * parameter is set to true, the query is excuted transaction save.
     * 
     * @param ezcQuery $q 
     * @param bool $transaction
     * @return PDOStatement
     *
     * @access private
     */
    public function performQuery( ezcQuery $q, $transaction = false )
    {
        if ( $transaction )
        {
            $this->database->beginTransaction();
        }
        try
        {
            $stmt = $q->prepare();
            $stmt->execute();
            if ( ( $errCode = $stmt->errorCode() ) != 0 )
            {
                if ( $transaction )
                {
                    $this->database->rollback();
                }
                throw new ezcPersistentQueryException( "The query returned error code $errCode.", $q );
            }
            if ( $transaction )
            {
                $this->database->commit();
            }
            return $stmt;
        }
        catch ( PDOException $e )
        {
            if ( $transaction )
            {
                $this->database->rollback();
            }
            throw new ezcPersistentQueryException( $e->getMessage(), $q );
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
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
            case 'database':
            case 'definitionManager':
            case 'loadHandler':
            case 'saveHandler':
            case 'deleteHandler':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
                break;
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
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property propertys is not an instance of
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
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

    /**
     * Checks many-to-many relation between persistent objects.
     *
     * Checks if the object defined by $relState is related to the object 
     * defined by $srcState. Creates the corresponding SELECT query and checks 
     * the result.
     * 
     * @param array $srcState 
     * @param ezcPersistentObjectDefinition $srcDef 
     * @param array $relState 
     * @param ezcPersistentObjectDefinition $relDef 
     * @param ezcPersistentRelation $relationDef 
     * @return bool
     */
    private function checkComplexRelation( array $srcState, ezcPersistentObjectDefinition $srcDef, array $relState, ezcPersistentObjectDefinition $relDef, ezcPersistentRelation $relationDef )
    {
        $q = $this->database->createSelectQuery();
        $q->select( $q->expr->count( '*' ) );
        $q->from( $relationDef->relationTable );

        foreach ( $relationDef->columnMap as $colMap )
        {
            $q->where(
                $q->expr->lAnd(
                    $q->expr->eq(
                        $this->database->quoteIdentifier( $colMap->relationSourceColumn ),
                        $q->bindValue( $srcState[$srcDef->columns[$colMap->sourceColumn]->propertyName] )
                    ),
                    $q->expr->eq(
                        $this->database->quoteIdentifier( $colMap->relationDestinationColumn ),
                        $q->bindValue( $relState[$relDef->columns[$colMap->destinationColumn]->propertyName] )
                    )
                )
            );
        }
        $stmt = $q->prepare();
        $stmt->execute();

        return ( $stmt->fetchColumn() != 0 );
    }


    /**
     * Checks simple relation between persistent objects.
     *
     * Simple relations are {@link ezcPersistentOneToOneRelation}, {@link 
     * ezcPersistentOneToManyRelation} and {@link 
     * ezcPersistentManyToOneRelation}. Checks if the object defined by 
     * $relState is related to the object defined by $srcState. Does not 
     * perform a database query for checking..
     * 
     * @param array $srcState 
     * @param ezcPersistentObjectDefinition $srcDef 
     * @param array $relState 
     * @param ezcPersistentObjectDefinition $relDef 
     * @param ezcPersistentRelation $relationDef 
     * @return bool
     */
    private function checkSimpleRelation( array $srcState, ezcPersistentObjectDefinition $srcDef, array $relState, ezcPersistentObjectDefinition $relDef, ezcPersistentRelation $relationDef )
    {
        foreach ( $relationDef->columnMap as $colMap )
        {
            $srcProp = $srcDef->columns[$colMap->sourceColumn]->propertyName;
            $relProp = $relDef->columns[$colMap->destinationColumn]->propertyName;
            if ( $srcState[$srcProp] !== $relState[$relProp] )
            {
                return false;
            }
        }
        return true;
    }
}
?>
