<?php
/**
 * File containing the ezcDbSchemaDbReader interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the interface for database schema readers
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaDbReader extends ezcDbSchemaReader
{
    /**
     * Returns an ezcDbSchema created from the database schema in the database referenced by $db
     *
     * This method analyses the current database referenced by $db and creates
     * a schema definition out of this. This schema definition is returned as
     * an (@link ezcDbSchema) object.
     *
     * @param ezcDbHandler $db
     * @return ezcDbSchema
     */
    public function loadFromDb( ezcDbHandler $db );
}
?>
