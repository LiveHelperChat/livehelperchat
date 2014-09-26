<?php
/**
 * File containing the ezcPersistentObjectNotFoundException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if an object to be loaded could not be found.
 *
 * @see ezcPersistentSession::load()
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentObjectNotFoundException extends ezcPersistentQueryException
{
    /**
     * Creates a new exception for the object of $class with $id.
     *
     * @param string $class 
     * @param mixed $id 
     */
    public function __construct( $class, $id )
    {
        parent::__construct( "No object of class '$class' with id '$id'." );
    }
}

?>
