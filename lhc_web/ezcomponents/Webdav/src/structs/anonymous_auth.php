<?php
/**
 * File containing the ezcWebdavAnonymousAuth struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Struct representing an anonymous user.
 *
 * This struct is used to indicate a missing or non-parsable Authorization
 * header. The user must be handled as if he was not authenticated. The
 * $username is empty.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavAnonymousAuth extends ezcWebdavAuth
{
    /**
     * Creates a new basic auth credential struct.
     * 
     * It is not possible to define a $username, since the anonymous user
     * always has the $username ''.
     */
    public function __construct()
    {
        parent::__construct( '' );
    }
}

?>
