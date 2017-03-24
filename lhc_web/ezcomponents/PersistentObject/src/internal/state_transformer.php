<?php
/**
 * File containing the ezcPersistentStateTransformer class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This internal class provides functionality to transform between
 * row and state arrays.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @access private
 */
class ezcPersistentStateTransformer
{
    /**
     * Returns the the row $row retrieved from PDO transformed into a state array
     * that can be used to set the state on a persistent object.
     *
     * $def holds the definition of the persistent object the $row maps to.
     *
     * The most basic task is to transform the database column names into
     * property names.
     *
     * @throws ezcPersistentException if a fatal error occured during the transformation
     * @param array $row
     * @param ezcPersistentObjectDefinition $def
     * @return array
     */
    public static function rowToStateArray( array $row, ezcPersistentObjectDefinition $def )
    {
        // Sanity check for reverse-lookup
        // Issue #12108
        if ( count( $def->columns ) === 0 )
        {
            throw new ezcPersistentObjectException(
                "The PersistentObject definition for class {$def->class} was not initialized correctly.",
                'Missing reverse lookup for columns. Check the definition manager.'
            );
        }

        $result = array();
        foreach ( $row as $key => $value )
        {
            if ( $key === $def->idProperty->resultColumnName )
            {
                $result[$def->idProperty->propertyName] = ($def->idProperty->propertyType == ezcPersistentObjectProperty::PHP_TYPE_INT ? (int)$value : $value);
            }
            else
            {
                if (isset($def->columns[$key])) {             
                    $result[$def->columns[$key]->propertyName] = ( 
                        !is_null( $def->columns[$key]->converter )
                            ? $def->columns[$key]->converter->fromDatabase( $value )
                            : ($def->columns[$key]->propertyType == ezcPersistentObjectProperty::PHP_TYPE_INT ? (int)$value : $value));
                } else {
                    $result['virtual_' . $key] = $value;
                }
            }
        }
        return $result;
    }
}

?>
