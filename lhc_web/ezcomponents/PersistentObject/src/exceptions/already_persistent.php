<?php
/**
 * File containing the ezcPersistentObjectAlreadyPersistentException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when a method that requires a non-persistent object is provided
 * an object that is already persistent.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentObjectAlreadyPersistentException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentObjectAlreadyPersistentException for the class
     * $class.
     *
     * @param string $class
     * @return void
     */
    public function __construct( $class )
    {
        parent::__construct( "The object of type $class is already persistent." );
    }
}
?>
