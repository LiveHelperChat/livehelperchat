<?php
/**
 * File containing the ezcPersistentRelationNotFoundException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, if a desired relation between 2 classes was not found.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentRelationNotFoundException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentRelationNotFoundException for the class $class
     * which does not have a relation for $relatedClass.
     *
     * @param string $class
     * @param string $relatedClass
     * @param string $relationName
     * @return void
     */
    public function __construct( $class, $relatedClass, $relationName = null )
    {
        parent::__construct(
            "Class '{$class}' does not have a relation to '{$relatedClass}'"
                . ( $relationName !== null ? " with name '$relationName'." : '.' )
        );
    }
}
?>
