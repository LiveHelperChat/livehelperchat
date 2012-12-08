<?php
/**
 * File containing the ezcWebdavDigestAuthenticator interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for Digest authentication mechanism.
 *
 * This interface must be implemented by objects that provide authentication
 * through a username/password combination, as defined by the HTTP Digest
 * authentication method.
 *
 * An instance of a class implementing this interface may be used in the {@link
 * ezcWebdavServer} $auth property. The WebDAV server will then use this
 * instance to perform authentication. In addition, classes may implement
 * {@link ezcWebdavBasicAuthenticator} and are highly recommended to do so.
 *
 * @see ezcWebdavServer
 * @see ezcWebdavBasicAuthenticator
 * @see ezcWebdavAuthorizer
 * @see ezcWebdavDigestAuth
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavDigestAuthenticator extends ezcWebdavBasicAuthenticator
{
    /**
     * Checks authentication for the given $data.
     *
     * This method performs authentication as defined by the HTTP Digest
     * authentication mechanism. The received struct contains all information
     * necessary.
     *
     * If authentication succeeded true is returned, otherwise false.
     * 
     * @param ezcWebdavDigestAuth $data 
     * @return bool
     */
    public function authenticateDigest( ezcWebdavDigestAuth $data );
}

?>
