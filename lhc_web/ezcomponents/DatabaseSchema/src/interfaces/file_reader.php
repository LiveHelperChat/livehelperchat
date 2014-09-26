<?php
/**
 * File containing the ezcDbSchemaFileReader interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the interface for file schema readers
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaFileReader extends ezcDbSchemaReader
{
    /**
     * Returns an ezcDbSchema with the definition from $file
     *
     * @param string $file
     * @return ezcDbSchema
     */
    public function loadFromFile( $file );
}
?>
