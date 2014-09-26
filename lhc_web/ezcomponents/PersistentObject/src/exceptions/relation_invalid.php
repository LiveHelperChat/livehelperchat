<?php
/**
 * File containing the ezcPersistentRelationInvalidException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, if the class of a relation definition was invalid.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentRelationInvalidException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentRelationInvalidException for the given
     * relation class $class.
     *
     * @param string $class
     * @return void
     */
    public function __construct( $class )
    {
        parent::__construct( "Class '{$class}' is not a valid relation defitinion class." );
    }
}
?>
