<?php
/**
 * File containing the ezcDbSchemaDiffWriter interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the base interface for schema difference writers
 *
 * This interface is extended by both a specific interface for schema
 * difference writers which write to a file (@link ezcDbSchemaDiffFileWriter)
 * and one for writers that apply differences directly to a database instance
 * (@link ezcDbSchemaDiffDbWriter).
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaDiffWriter
{
    /**
     * Returns what type of schema difference writer this class implements.
     *
     * Depending on the class it either returns ezcDbSchema::DATABASE (for
     * writers that apply the differences directly to a database) or
     * ezcDbSchema::FILE (for writers that write the differences to a file).
     *
     * @return int
     */
    public function getDiffWriterType();
}
?>
