<?php
/**
 * File containing the ezcWebdavBackendMakeCollection interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Interface implemented by backends which support the MKCOL operation.
 *
 * If a backend supports the MKCOL request method, it must implement this
 * interface.
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavBackendMakeCollection
{
    /**
     * Serves MKCOL (make collection) requests.
     *
     * The method receives a {@link ezcWebdavMakeCollectionRequest} objects
     * containing all relevant information obout the clients request and will
     * return an instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavMakeCollectionResponse} on success.
     * 
     * @param ezcWebdavMakeCollectionRequest $request 
     * @return ezcWebdavResponse
     */
    public function makeCollection( ezcWebdavMakeCollectionRequest $request );
}

?>
