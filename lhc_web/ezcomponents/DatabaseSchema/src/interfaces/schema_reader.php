<?php
/**
 * File containing the ezcDbSchemaReader interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the base interface for schema readers.
 *
 * This interface is extended by both a specific interface for schema readers
 * who read from a file (@link ezcDbSchemaFileReader) and one for readers that
 * read from a database (@link ezcDbSchemaDbReader).
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaReader
{
    /**
     * Returns what type of schema reader this class implements.
     *
     * Depending on the class it either returns ezcDbSchema::DATABASE (for
     * readers that read from a database) or ezcDbSchema::FILE (for readers
     * that read from a file).
     *
     * @return int
     */
    public function getReaderType();
}
?>
