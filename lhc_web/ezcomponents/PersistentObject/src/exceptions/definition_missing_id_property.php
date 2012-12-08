<?php
/**
 * File containing the ezcPersistentDefinitionMissingIdPropertyException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown, if an object of ezcPersistentObjectDefinition has no idProperty attribute.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentDefinitionMissingIdPropertyException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentDefinitionMissingIdPropertyException for the class $class.
     *
     * @param string $class
     * @return void
     */
    public function __construct( $class )
    {
        parent::__construct( "The persistent object definition for the class '$class' does not have an 'idProperty' attribute defined." );
    }
}
?>
