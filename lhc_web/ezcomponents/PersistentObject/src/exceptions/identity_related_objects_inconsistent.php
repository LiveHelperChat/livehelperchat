<?php
/**
 * File containing the ezcPersistentIdentityRelatedObjectsInconsistentException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a set of related objects is inconsistent. 
 *
 * {@link ezcPersistentIdentityMap::setRelatedObjects()} and {@link
 * ezcPersistentIdentityMap::setRelatedObjectSet()}  will throw this exception,
 * if any of the objects in the set of related objects is not of the given
 * related class.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentIdentityRelatedObjectsInconsistentException extends ezcPersistentObjectException
{

    /**
     * Creates a new ezcPersistentIdentityRelatedObjectsInconsistentException.
     *
     * Creates a new ezcPersistentIdentityRelatedObjectsInconsistentException.
     * The source object is of $class with $id, the related objects are
     * expected to be of $expectedClass, but the $actualClass was found.
     *
     * @param string $class
     * @param mixed $id
     * @param string $expectedClass
     * @param string $actualClass
     */
    public function __construct( $class, $id, $expectedClass, $actualClass )
    {
        parent::__construct(
            sprintf(
                "Inconsistent relation set for object of class '%s' with ID '%s'. '%s' was expected, but '%s' was found.",
                $class,
                $id,
                $expectedClass,
                $actualClass
            )
        );
    }
}
?>
