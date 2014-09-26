<?php
/**
 * File containing the ezcDbSchemaException class
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception that is thrown if an invalid class is passed as schema reader to the manager.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaInvalidSchemaException extends ezcDbSchemaException
{
    /**
     * Constructs an ezcDbSchemaInvalidSchemaException with an optional message.
     *
     * @param string $message
     */
    function __construct( $message = null )
    {
        $messagePart = $message !== null ? " ($message)" : "";
        parent::__construct( "The schema is invalid.$messagePart" );
    }
}
?>
