<?php
/**
 * File containing the ezcPersistentIdentityMissingException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if an identity is expected to be recorded, but is not found.
 *
 * {@link ezcPersistentIdentityMap::addRelatedObject()} will throw this
 * exception, if the identity of the source or of the related object does not
 * exist. In addition {@link ezcPersistentIdentityMap::removeRelatedObject()}
 * if its source object identity is not found.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentIdentityMissingException extends ezcPersistentObjectException
{

    /**
     * Creates a new ezcPersistentIdentityMissingException.
     *
     * Creates a new ezcPersistentIdentityMissingException for the object of
     * $class with ID $id.
     *
     * @param string $class
     * @param mixed $id
     * @param string $relatedClass
     * @param string $relationName
     */
    public function __construct( $class, $id )
    {
        parent::__construct(
            "The identity of the object of class '{$class}' with ID '{$id}' was expected to exists, but not found in the identity map."
        );
    }
}
?>
