<?php
/**
 * File containing the ezcDatabaseSchemaUnknownFormatException class
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception that is thrown if no configuration object has been set to operate
 * on. The operation cannot continue with this object.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaUnknownFormatException extends ezcDbSchemaException
{
    /**
     * Constructs an ezcDatabaseSchemaUnknownFormatException for the $format and handler type $type.
     *
     * @param string $format
     * @param string $type
     */
    function __construct( $format, $type )
    {
        parent::__construct( "There is no '{$type}' handler available for the '{$format}' format." );
    }
}
?>
