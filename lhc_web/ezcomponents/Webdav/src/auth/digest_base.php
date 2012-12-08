<?php
/**
 * File containing the abstract ezcWebdavDigestAuthenticatorBase class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for ezcWebdavDigestAuthenticator implementations.
 *
 * This base class provides a method for calculating and checking of digest
 * information. If you don't want to implement the necessary algorithms
 * yourself, you can extend this base class.
 *
 * It is recommended to perform the digest calculation outside of PHPs scope to
 * not load the clear text passwords into PHP memory. One possibility would be
 * to use a stored procedure in your database. However, you might not have this
 * possibility.
 *
 * @see ezcWebdavDigestAuthenticator
 * @see ezcWebdavDigestAuth
 * 
 * @package Webdav
 * @version 1.1.4
 */
abstract class ezcWebdavDigestAuthenticatorBase implements ezcWebdavDigestAuthenticator
{
    /**
     * Calculates the digest according to $data and $password and checks it.
     *
     * This method receives digest data in $data and a plain text $password for
     * the digest user. It automatically calculates the digest and veryfies it
     * against the $response property of $data.
     *
     * The method returns true, if the digest matched the response, otherwise
     * false.
     *
     * Use this helper method to avoid manually calculating the digest
     * yourself. The submitted $data should be received by {@link
     * authenticateDigest()} and the $password should be read from your
     * authentication back end.
     *
     * For security reasons it is recommended to calculate and verify the
     * digest somewhere else (e.g. in a stored procedure in your database),
     * without loading it as plain text into PHP memory.
     * 
     * @param ezcWebdavDigestAuth $data 
     * @param string $password 
     * @return bool
     */
    protected function checkDigest( ezcWebdavDigestAuth $data, $password )
    {
        $ha1 = md5( "{$data->username}:{$data->realm}:{$password}" );
        $ha2 = md5( "{$data->requestMethod}:{$data->uri}" );

        $digest = null;
        if ( !empty( $data->nonceCount ) && !empty( $data->clientNonce ) && !empty( $data->qualityOfProtection ) )
        {
            // New digest (RFC 2617)
            $digest = md5(
                "{$ha1}:{$data->nonce}:{$data->nonceCount}:{$data->clientNonce}:{$data->qualityOfProtection}:{$ha2}"
            );
        }
        else
        {
            // Old digest (RFC 2069)
            $digest = md5( "{$ha1}:{$data->nonce}:{$ha2}" );
        }

        return $digest === $data->response;
    }
    
    /**
     * Checks authentication for the given $data.
     *
     * This method performs authentication as defined by the HTTP Digest
     * authentication mechanism. The received struct contains all information
     * necessary.
     *
     * If authentication succeeded true is returned, otherwise false.
     *
     * You can use {@link checkDigest()} to perform the actual digest
     * calculation and compare it to the response field.
     * 
     * @param ezcWebdavDigestAuth $data 
     * @return bool
     */
    // abstract public function authenticateDigest( ezcWebdavDigestAuth $data );

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
    // abstract public function authenticateBasic( ezcWebdavBasicAuth $data );
}

?>
