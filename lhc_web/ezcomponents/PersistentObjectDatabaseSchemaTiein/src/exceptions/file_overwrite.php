<?php
/**
 * File containing the ezcPersistentObjectSchemaOverwriteException class.
 *
 * @package PersistentObjectDatabaseSchemaTiein
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown if a file to be written exists, but overwrite is disabled.
 * 
 * @package PersistentObjectDatabaseSchemaTiein
 * @version 1.3
 */
class ezcPersistentObjectSchemaOverwriteException extends ezcBaseFileException
{
    /**
     * Creates a new exception, affecting $file.
     * 
     * @param string $file 
     * @return void
     */
    public function __construct( $file )
    {
        parent::__construct( "The file '$file' exists, but overwriting is disabled." );
    }
}

?>
