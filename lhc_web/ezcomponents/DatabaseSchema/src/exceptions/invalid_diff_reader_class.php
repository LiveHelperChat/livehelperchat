<?php
/**
 * File containing the ezcDbSchemaInvalidDiffReaderClassException class
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception that is thrown if an invalid class is passed as schema difference reader to the manager.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaInvalidDiffReaderClassException extends ezcDbSchemaException
{
    /**
     * Constructs an ezcDbSchemaInvalidDiffReaderClassException for reader class $readerClass
     *
     * @param string $readerClass
     */
    function __construct( $readerClass )
    {
        parent::__construct( "Class '{$readerClass}' does not exist, or does not implement the 'ezcDbSchemaDiffReader' interface." );
    }
}
?>
