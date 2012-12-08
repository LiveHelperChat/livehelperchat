<?php
/**
 * File containing the ezcWebdavLockAdministrationException class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if an error occurs in the administrator class.
 *
 * The {@link ezcWebdavLockAdministrator} class takes a special role in the
 * lock plugin, since it does not operate in the server, but allows you to
 * administrate the locks in your backend. If any kind of error occurs during
 * an administrative process, this exception is thrown.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavLockAdministrationException extends ezcWebdavException
{
    /**
     * Creates a new excption.
     *
     * $message explains the error. $error contains the response created by the
     * backend, if this was the reason for the exception.
     * 
     * @param mixed $message 
     * @param ezcWebdavErrorResponse $error 
     */
    public function __construct( $message, ezcWebdavErrorResponse $error = null )
    {
        parent::__construct(
            $message . ( $error !== null ? ' (' . (string) $error . ')' : '' )
        );
    }
}

?>
