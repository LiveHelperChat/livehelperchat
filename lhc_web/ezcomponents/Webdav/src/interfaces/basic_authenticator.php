<?php
/**
 * File containing the ezcWebdavBasicAuthenticator interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for Basic authentication mechanism.
 *
 * This interface must be implemented by objects that provide authentication
 * through a username/password combination, as defined by the HTTP Basic
 * authentication method.
 *
 * An instance of a class implementing this interface may be used in the {@link
 * ezcWebdavServer} $auth property. The WebDAV server will then use this
 * instance to perform authentication. In addition, classes may implement
 * {@link ezcWebdavDigestAuthenticator} and are highly recommended to do so.
 *
 * @see ezcWebdavServer
 * @see ezcWebdavDigestAuthenticator
 * @see ezcWebdavAuthorizer
 * @see ezcWebdavBasicAuth
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavBasicAuthenticator extends ezcWebdavAnonymousAuthenticator
{
    /**
     * Checks authentication for the given $user.
     *
     * This method checks the given user/password credentials encapsulated in
     * $data. Returns true if the user was succesfully recognized and the
     * password is valid for him, false otherwise. In case no username and/or
     * password was provided in the request, empty strings are provided as the
     * parameters of this method.
     * 
     * @param ezcWebdavBasicAuth $data
     * @return bool
     */
    public function authenticateBasic( ezcWebdavBasicAuth $data );
}

?>
