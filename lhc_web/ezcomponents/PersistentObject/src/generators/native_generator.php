<?php
/**
 * File containing the ezcPersistentNativeGenerator class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Generates IDs based on the PDO::lastInsertId method.
 *
 * It is recommended to use auto_increment id columns for databases supporting
 * it. This includes MySQL and SQLite. Other databases need to create a sequence
 * per table.
 *
 * auto_increment databases:
 * <code>
 *  CREATE TABLE test
 *  ( id integer unsigned not null auto_increment, PRIMARY KEY (id ));
 * </code>
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentNativeGenerator extends ezcPersistentIdentifierGenerator
{
    /**
     * No functionality, since database handles ID generation automatically.
     *
     * @param ezcPersistentObjectDefinition $def
     * @param ezcDbHandler $db
     * @param ezcQueryInsert $q
     * @return void
     */
    public function preSave( ezcPersistentObjectDefinition $def, ezcDbHandler $db, ezcQueryInsert $q )
    {
    }

    /**
     * Returns the integer value of the generated identifier for the new object.
     * Called right after execution of the insert query.
     *
     * @param ezcPersistentObjectDefinition $def
     * @param ezcDbHandler $db
     * @return int
     */
    public function postSave( ezcPersistentObjectDefinition $def, ezcDbHandler $db )
    {
        $id = (int)$db->lastInsertId();
        // check that the value was in fact successfully received.
        if ( $db->errorCode() != 0 )
        {
            return null;
        }
        return $id;
    }
}

?>
