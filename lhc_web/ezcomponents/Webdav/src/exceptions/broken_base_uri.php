<?php
/**
 * File containing the ezcWebdavBrokenBaseUriException class
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if an incorrect base URI is given to the basic path factory.
 *
 * <code>
 * <?php
 *  $server->configurations[0]->pathFactory =
 *      new ezcWebdavBasicPathFactory( '/no/uri/path' );
 * ?>
 * </code>
 *
 * @see ezcWebdavBasicPathFactory
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavBrokenBaseUriException extends ezcWebdavException
{
    /**
     * Initializes the exception with the given $uri and optional $msg.
     *
     * @param string $uri
     * @param string $msg
     */
    public function __construct( $uri, $msg = null )
    {
        parent::__construct(
            "The string '{$uri}' is not a valid URI to initialize the path factory." .
            ( $msg !== null ? " $msg" : '' )
        );
    }
}

?>
