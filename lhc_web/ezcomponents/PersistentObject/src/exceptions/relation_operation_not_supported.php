<?php
/**
 * File containing the ezcPersistentRelationOperationNotSupportedException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, if the given relation class ćould not be found.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentRelationOperationNotSupportedException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentRelationOperationNotSupportedException for
     * the $class which does not support the $operation in respect to
     * $relatedClass. Optionally a $reason can be given.
     *
     * @param string $class
     * @param string $relatedClass
     * @param string $operation
     * @param string $reason
     * @return void
     */
    public function __construct( $class, $relatedClass, $operation, $reason = null )
    {
        parent::__construct(
            "The relation between '{$class}' and '{$relatedClass}' does not support the operation '{$operation}'." .
                ( $reason !== null ? " Reason: '{$reason}'." : "" )
        );
    }
}
?>
