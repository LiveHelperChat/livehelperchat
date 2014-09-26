<?php
/**
 * File containing the ezcDbSchemaWriter interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the base interface for schema writers.
 *
 * This interface is extended by both a specific interface for schema writers
 * who writer to a file (@link ezcDbSchemaFileWriter) and one for writers which 
 * create tables in a database (@link ezcDbSchemaDbWriter).
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaWriter
{
    /**
     * Returns what type of schema writer this class implements.
     *
     * Depending on the class it either returns ezcDbSchema::DATABASE (for
     * writers that create tables in a database) or ezcDbSchema::FILE (for writers
     * that writer schema definitions to a file).
     *
     * @return int
     */
    public function getWriterType();
}
?>
