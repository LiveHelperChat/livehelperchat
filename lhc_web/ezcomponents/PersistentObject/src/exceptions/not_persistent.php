<?php
/**
 * File containing the ezcPersistentObjectNotPersistentException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when a method that requires a persistent object is provided
 * an object not yet persistent.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentObjectNotPersistentException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentObjectNotPersistentException for the class
     * $class.
     *
     * @param string $class
     * @return void
     */
    public function __construct( $class )
    {
        parent::__construct( "The object of type $class is not persistent." );
    }
}
?>
