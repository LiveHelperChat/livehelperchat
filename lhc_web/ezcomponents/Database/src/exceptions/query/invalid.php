<?php
/**
 * File containing the ezcQueryException class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for exceptions related to the SQL abstraction.
 *
 * @package Database
 * @version 1.4.7
 */
class ezcQueryInvalidException extends ezcQueryException
{
    /**
     * Constructs a QueryInvalid exception with the type $type and the
     * additional information $message.
     *
     * $type should be used to specify the type of the query that failed.
     * Possible values are SELECT, INSERT, UPDATE and DELETE.
     *
     * Use $message to specify exactly what went wrong.
     *
     * @param string $type
     * @param string $message
     */
    public function __construct( $type, $message )
    {
        $info = "The '{$type}' query could not be built. {$message}";
        parent::__construct( $message );
    }
}
?>
