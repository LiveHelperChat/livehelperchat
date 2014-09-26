<?php
/**
 * File containing the ezcDbSchemaPhpArrayReader class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler that reads database definitions and database difference definitions from a file containing a PHP variable.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaPhpArrayReader implements ezcDbSchemaFileReader, ezcDbSchemaDiffFileReader
{
    /**
     * Returns what type of reader writer this class implements.
     *
     * This method always returns ezcDbSchema::FILE
     *
     * @return int
     */
    public function getReaderType()
    {
        return ezcDbSchema::FILE;
    }

    /**
     * Returns what type of schema difference reader this class implements.
     *
     * This method always returns ezcDbSchema::FILE
     *
     * @return int
     */
    public function getDiffReaderType()
    {
        return ezcDbSchema::FILE;
    }

    /**
     * Returns the database schema stored in the file $file
     *
     * @throws ezcBaseFileNotFoundException if the file $file could not be
     *         found.
     * @throws ezcDbSchemaInvalidSchemaException if the data in the $file is
     *         corrupt or when the file could not be opened.
     *
     * @param string $file
     * @return ezcDbSchema
     */
    public function loadFromFile( $file )
    {
        if ( !file_exists( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file, 'schema' );
        }

        $schema = include $file;
        if ( !is_array( $schema ) || count( $schema ) != 2 )
        {
            throw new ezcDbSchemaInvalidSchemaException( 'File does not have the correct structure' );
        }
        // @TODO: Add validator call here

        return new ezcDbSchema( $schema[0], $schema[1] );
    }
    
    /**
     * Returns the database differences stored in the file $file
     *
     * @throws ezcBaseFileNotFoundException if the file $file could not be
     *         found.
     * @throws ezcDbSchemaInvalidSchemaException if the data in the $file is
     *         corrupt or when the file could not be opened.
     *
     * @param string $file
     * @return ezcDbSchemaDiff
     */
    public function loadDiffFromFile( $file )
    {
        if ( !file_exists( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file, 'differences schema' );
        }

        $schema = include $file;
        if ( !is_object( $schema ) || get_class( $schema ) != 'ezcDbSchemaDiff' )
        {
            throw new ezcDbSchemaInvalidSchemaException( 'File does not have the correct structure' );
        }

        return $schema;
    }
}
?>
