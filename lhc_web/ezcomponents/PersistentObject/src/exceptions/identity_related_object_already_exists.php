<?php
/**
 * File containing the ezcPersistentIdentityRelatedObjectAlreadyExistsException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a related objects is added twice to a set of related objects.
 *
 * {@link ezcPersistentIdentityMap::addRelatedObject()} throws this exception,
 * if the same related object is added twice.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentIdentityRelatedObjectAlreadyExistsException extends ezcPersistentObjectException
{

    /**
     * Creates a new ezcPersistentIdentityRelatedObjectAlreadyExistsException.
     *
     * Creates a new ezcPersistentIdentityRelatedObjectAlreadyExistsException
     * for the object of $class with ID $id and the related objects of class
     * $relatedClass, with optional set name $relationName.
     *
     * @param string $class
     * @param mixed $id
     * @param string $relatedClass
     * @param mixed $relatedId
     * @param string $relationName
     */
    public function __construct( $class, $id, $relatedClass, $relatedId, $relationName = null )
    {
        parent::__construct(
            sprintf(
                "The object of class '%s' with ID '%s' is already related to the object of class '%s' with ID '%s'%s.",
                $relatedClass,
                $relatedId,
                $class,
                $id,
                ( $relationName !== null ? " over the relation '$relationName'" : '' )
            )
        );
    }
}
?>
