<?php
/**
 * File containing the ezcPersistentIdentityAlreadyExistsException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if an identity is duplicated.
 *
 * In case {@link ezcPersistentSessionIdentityDecorator::loadIntoObject()} is
 * used to load an already recorded identity into a second object or if {@link
 * ezcPersistentSessionIdentityDecorator::save()} is used to save a second
 * object with an already recorded identity. The latter case is only possible,
 * if {@link ezcPersistentManualGenerator} is used to create the objects
 * identifier.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentIdentityAlreadyExistsException extends ezcPersistentObjectException
{

    /**
     * Creates a new ezcPersistentIdentityAlreadyExistsException.
     *
     * Creates a new ezcPersistentIdentityAlreadyExistsException for the object
     * identified by $class and $id.
     *
     * @param string $class
     * @param mixed $id
     */
    public function __construct( $class, $id )
    {
        parent::__construct(
            "An identity for the object of '{$class}' with ID '{$id}' already exists in the identity map."
        );
    }
}
?>
