<?php
/**
 * File containing the ezcPersistentDefinitionManager class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Defines the interface for all persistent object definition managers.
 *
 * Definition managers are used to fetch the definition of a specific
 * persistent object. The definition is returned in form of a
 * ezcPersistentObjectDefinition structure.
 *
 * @version 1.7.1
 * @package PersistentObject
 */
abstract class ezcPersistentDefinitionManager
{
    /**
     * Returns the definition of the persistent object with the class $class.
     *
     * @throws ezcPersistentDefinitionNotFoundException if no such definition can be found.
     * @param string $class
     * @return ezcPersistentObjectDefinition
     */
    public abstract function fetchDefinition( $class );

    // public function storeDefinition( ezcPersistentObjectDefinition $def );

    /**
     * Returns the definition $def with the reverse relations field correctly set up.
     *
     * This method will go through all of the properties in the definition and set up
     * the columns field in the definition.
     *
     * @param ezcPersistentObjectDefinition $def The target persistent object definition.
     * @return ezcPersistentObjectDefinition
     */
    protected static function setupReversePropertyDefinition( ezcPersistentObjectDefinition $def )
    {
        foreach ( $def->properties as $field )
        {
            $def->columns[$field->resultColumnName] = $field;
        }
        if ( isset( $def->idProperty ) && $def->idProperty->columnName !== null )
        {
            $def->columns[$def->idProperty->resultColumnName] = $def->idProperty;
        }
        return $def;
    }
}
?>
