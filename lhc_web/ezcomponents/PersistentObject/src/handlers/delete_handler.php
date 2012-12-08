<?php
/**
 * File containing the ezcPersistentDeleteHandler class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Helper class for ezcPersistentSession to handle object deleting.
 *
 * An instance of this class is used internally in {@link ezcPersistentSession}
 * and takes care for deleting objects.
 * 
 * @package PersistentObject
 * @version 1.7.1
 * @access private
 */
class ezcPersistentDeleteHandler extends ezcPersistentSessionHandler
{
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
        $class   = get_class( $object );
        $def     = $this->definitionManager->fetchDefinition( $class );
        $state   = $this->session->getObjectState( $object );
        $idValue = $state[$def->idProperty->propertyName];

        // Check that the object is persistent already.
        // The < 0 check results from the times where only numeric IDs were allowed.
        if ( $idValue == null || $idValue < 0 )
        {
            throw new ezcPersistentObjectNotPersistentException( $class );
        }

        // Transaction safety for exceptions thrown while cascading.
        $this->database->beginTransaction();

        try
        {
            // Check for cascading relations to follow.
            foreach ( $def->relations as $relatedClass => $relation )
            {
                $this->cascadeDelete( $object, $relatedClass, $relation );
            }
        }
        catch ( Exception $e )
        {
            // Roll back the current transaction on any exception.
            $this->database->rollback();
            throw $e;
        }

        // Create and execute query.
        $q = $this->database->createDeleteQuery();
        $q->deleteFrom( 
            $this->database->quoteIdentifier( $def->table )
          )
          ->where(
            $q->expr->eq(
                $this->database->quoteIdentifier( $def->idProperty->columnName ),
                $q->bindValue( $idValue, null, $def->idProperty->databaseType )
            ) 
        );

        try
        {
            $this->session->performQuery( $q );
        }
        catch ( Exception $e )
        {
            $this->database->rollback();
            throw $e;
        }

        // After recursion of cascades everything should be fine here, or this
        // final commit call should perform the rollback ordered by a deeper level
        $this->database->commit();
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
     * If multiple relations between the class of $object and $relatedObject
     * are defined, the $relationName parameter is mandatory.
     *
     * @param object $object        Source object of the relation.
     * @param object $relatedObject Related object.
     * @param string $relationName  Relation name.
     *
     * @throws ezcPersistentRelationOperationNotSupportedException
     *         if a relation to create is marked as "reverse".
     * @throws ezcPersistentRelationNotFoundException
     *         if the deisred relation is not defined.
     */
    public function removeRelatedObject( $object, $relatedObject, $relationName = null )
    {
        $class        = get_class( $object );
        $relatedClass = get_class( $relatedObject );

        $def          = $this->definitionManager->fetchDefinition( $class );
        $relatedDef   = $this->definitionManager->fetchDefinition( get_class( $relatedObject ) );

        // Sanity checks.
        if ( !isset( $def->relations[$relatedClass] ) )
        {
            throw new ezcPersistentRelationNotFoundException( $class, $relatedClass );
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
        
        if ( isset( $relation->reverse ) && $relation->reverse === true )
        {
            throw new ezcPersistentRelationOperationNotSupportedException(
                $class,
                $relatedClass,
                "deleteRelation",
                "Relation is a reverse relation."
            );
        }

        $objectState        = $this->session->getObjectState( $object );
        $relatedObjectState = $this->session->getObjectState( $relatedObject );

        switch ( get_class( $relation ) )
        {
            case "ezcPersistentOneToManyRelation":
            case "ezcPersistentOneToOneRelation":
                foreach ( $relation->columnMap as $map )
                {
                    $relatedObjectState[
                        $relatedDef->columns[$map->destinationColumn]->propertyName
                    ] = null;
                }
                break;
            case "ezcPersistentManyToManyRelation":
                $q = $this->database->createDeleteQuery();
                $q->deleteFrom(
                    $this->database->quoteIdentifier( $relation->relationTable )
                );
                foreach ( $relation->columnMap as $map )
                {
                    $q->where(
                        $q->expr->eq(
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
                        ),
                        $q->expr->eq(
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
                        )
                    );
                }
                $this->session->performQuery( $q, true );
                break;
        }

        $relatedObject->setState( $relatedObjectState );
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
        $this->session->performQuery( $query, true );
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
        $def = $this->definitionManager->fetchDefinition( $class );

        $q = $this->database->createDeleteQuery();
        $q->setAliases( $this->session->generateAliasMap( $def, false ) );
        $q->deleteFrom( $this->database->quoteIdentifier( $def->table ) );

        return $q;
    }

    /**
     * Perform the cascading of deletes on a specific relation.
     *
     * This method checks a given $relation of a given $object for necessary
     * actions on a cascaded delete and performs them. If multiple relations
     * from $object class to $relatedClass exist, the $relationName is
     * mandatory.
     *
     * @param object $object
     * @param string $relatedClass
     * @param ezcPersistentRelation[Collection] $relation
     * @param string $relationName
     *
     * @todo Revise cascading code. So far it sends 1 delete statement per
     *       object but we can also collect them table wise and send just 1
     *       for each table.
     */
    private function cascadeDelete( $object, $relatedClass, $relation, $relationName = null )
    {
        // New multi-relations for a single class
        if ( $relation instanceof ezcPersistentRelationCollection )
        {
            foreach( $relation as $relationName => $realRelation )
            {
                $this->cascadeDelete( $object, $relatedClass, $realRelation, $relationName );
            }
        }

        // Remove relation records for ManyToMany relations
        if ( $relation instanceof ezcPersistentManyToManyRelation )
        {
            $relatedObjects = $this->session->loadHandler->getRelatedObjects(
                $object,
                $relatedClass,
                $relationName
            );
            foreach ( $relatedObjects as $relatedObject )
            {
                // Determine the correct direction for removal.
                if ( $relation->reverse === true  )
                {
                    $this->removeRelatedObject( $relatedObject, $object, $relationName );
                }
                else
                {
                    $this->removeRelatedObject( $object, $relatedObject, $relationName );
                }
            }
        }

        // Actually remove related objects
        if ( isset( $relation->cascade ) && $relation->cascade === true )
        {
            // Reverse relations never cascade
            // @todo: Is this correct? Or do we need to cascade reverse here?
            if ( isset( $relation->reverse ) && $relation->reverse === true )
            {
                throw new ezcPersistentRelationOperationNotSupported(
                    $class,
                    $relatedClass,
                    "cascade on delete",
                    "Reverse relations do not support cascading."
                );
            }
            $relatedObjects = $this->session->loadHandler->getRelatedObjects(
                $object,
                $relatedClass,
                $relationName
            );
            foreach ( $relatedObjects as $relatedObject )
            {
                $this->delete( $relatedObject );
            }
        }
    }
}

?>
