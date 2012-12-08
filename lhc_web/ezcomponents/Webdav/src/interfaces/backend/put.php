<?php
/**
 * File containing the ezcWebdavBackendPut interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Interface implemented by backends which support the PUT operation.
 *
 * If a backend supports the PUT request method, it must implement this
 * interface.
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavBackendPut
{
    /**
     * Serves PUT requests.
     *
     * The method receives a {@link ezcWebdavPutRequest} objects containing all
     * relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavPutResponse} on success.
     * 
     * @param ezcWebdavPutRequest $request 
     * @return ezcWebdavResponse
     */
    public function put( ezcWebdavPutRequest $request );
}

?>
