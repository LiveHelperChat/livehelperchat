<?php
/**
 * File containing the ezcPersistentObjectException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * General exception class for the PersistentObject package.
 *
 * All exceptions in the persistent object package are derived from this exception.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentObjectException extends ezcBaseException
{

    /**
     * Constructs a new ezcPersistentObjectException with error message $message and reason code $reason.
     *
     * Reason can be omitted if not applicable.
     *
     * @param string $message
     * @param string $reason
     * @return void
     */
    public function __construct( $message, $reason = null )
    {
        $message = $reason !== null ? "$message ($reason)" : $message;
        parent::__construct( $message );
    }
}
?>
