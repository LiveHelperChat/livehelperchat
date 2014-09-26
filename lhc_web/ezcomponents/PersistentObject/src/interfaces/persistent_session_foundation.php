<?php
/**
 * File containing the ezcPersistentSessionFoundation interface.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for ezcPersistentSession and its decorators.
 *
 * This interface defines the public methods of {@link ezcPersistentSession}
 * and decorators for this class. The interface needs to be implemented by any
 * class that can be used like {@link ezcPersistentSession}, especially, if
 * this class shall be used with {@link ezcPersistentSessionInstance}.
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
interface ezcPersistentSessionFoundation
{
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
    function load( $class, $id );

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
    function loadIfExists( $class, $id );

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
    function loadIntoObject( $object, $id );

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
    function refresh( $object );

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
    function find( $query, $class = null );

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
    function findIterator( $query, $class = null );

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
    function getRelatedObjects( $object, $relatedClass, $relationName = null );

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
    function getRelatedObject( $object, $relatedClass, $relationName = null );

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
    function createFindQuery( $class );

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
    function createRelationFindQuery( $object, $relatedClass, $relationName = null );

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
    function save( $object );

    /**
     * Saves the new persistent object $object to the database using an UPDATE query.
     *
     * @throws ezcPersistentDefinitionNotFoundException if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectNotPersistentException if $object is not stored in the database already.
     * @throws ezcPersistentQueryException
     * @param object $object
     * @return void
     */
    function update( $object );

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
    function saveOrUpdate( $object );

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
    function addRelatedObject( $object, $relatedObject, $relationName = null );

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
    function createUpdateQuery( $class );

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
    function updateFromQuery( ezcQueryUpdate $query );

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
    function delete( $object );

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
    function removeRelatedObject( $object, $relatedObject, $relationName = null );

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
    function deleteFromQuery( ezcQueryDelete $query );

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
    function createDeleteQuery( $class );

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
    function generateAliasMap( ezcPersistentObjectDefinition $def, $prefixTableName = true );

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
    function getColumnsFromDefinition( ezcPersistentObjectDefinition $def, $prefixTableName = true );
}

?>
