<?php
/**
 * File containing the ezcDbSchemaInvalidWriterClassException class
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception that is thrown if an invalid class is passed as schema writer to the manager.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaInvalidWriterClassException extends ezcDbSchemaException
{
    /**
     * Constructs an ezcDbSchemaInvalidWriterClassException for writer class $writerClass
     *
     * @param string $writerClass
     */
    function __construct( $writerClass )
    {
        parent::__construct( "Class '{$writerClass}' does not exist, or does not implement the 'ezcDbSchemaWriter' interface." );
    }
}
?>
