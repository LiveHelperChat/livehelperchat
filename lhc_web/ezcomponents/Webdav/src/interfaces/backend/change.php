<?php
/**
 * File containing the ezcWebdavBackendChange interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Interface implemented by backends which support the DELETE, COPY and MOVE operations.
 *
 * If a backend supports the following request method, it must implement this
 * interface:
 *
 * <ul>
 *     <li>DELETE</li>
 *     <li>COPY</li>
 *     <li>MOVE</li>
 * </ul>
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavBackendChange
{
    /**
     * Serves DELETE requests.
     *
     * The method receives a {@link ezcWebdavDeleteRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavDeleteResponse} on success.
     * 
     * @param ezcWebdavDeleteRequest $request 
     * @return ezcWebdavResponse
     */
    public function delete( ezcWebdavDeleteRequest $request );

    /**
     * Serves COPY requests.
     *
     * The method receives a {@link ezcWebdavCopyRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavCopyResponse} on success. If only some operations failed, this
     * method may return an instance of {@link ezcWebdavMultistatusResponse}.
     * 
     * @param ezcWebdavCopyRequest $request 
     * @return ezcWebdavResponse
     */
    public function copy( ezcWebdavCopyRequest $request );

    /**
     * Serves MOVE requests.
     *
     * The method receives a {@link ezcWebdavMoveRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavMoveResponse} on success. If only some operations failed, this
     * method may return an instance of {@link ezcWebdavMultistatusResponse}.
     * 
     * @param ezcWebdavMoveRequest $request 
     * @return ezcWebdavResponse
     */
    public function move( ezcWebdavMoveRequest $request );
}

?>
