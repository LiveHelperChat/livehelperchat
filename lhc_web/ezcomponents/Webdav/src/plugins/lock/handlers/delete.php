<?php
/**
 * File containing the ezcWebdavLockDeleteRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the DELETE request.
 *
 * This class provides plugin callbacks for the DELETE request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockDeleteRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * Handles DELETE requests.
     *
     * Performs all lock related checks necessary for the DELETE request. In case
     * a violation with locks is detected or any other pre-condition check
     * fails, this method returns an instance of {@link ezcWebdavResponse}. If
     * everything is correct, null is returned, so that the $request is handled
     * by the backend.
     *
     * @param ezcWebdavRequest $request ezcWebdavDeleteRequest
     * @return ezcWebdavResponse|null
     */
    public function receivedRequest( ezcWebdavRequest $request )
    {
        $ifHeader = $request->getHeader( 'If' );

        $targetLockRefresher = null;
        if ( $ifHeader !== null )
        {
            $targetLockRefresher = new ezcWebdavLockRefreshRequestGenerator(
                $request
            );
        }

        $violation = $this->tools->checkViolations(
            new ezcWebdavLockCheckInfo(
                $request->requestUri,
                ezcWebdavRequest::DEPTH_INFINITY,
                $request->getHeader( 'If' ),
                $request->getHeader( 'Authorization' ),
                ezcWebdavAuthorizer::ACCESS_WRITE,
                $targetLockRefresher
            ),
            true
        );

        // Lock refresh must occur no matter if the request succeeds
        if ( $targetLockRefresher !== null )
        {
            $targetLockRefresher->sendRequests();
        }

        if ( $violation !== null )
        {
            // ezcWebdavErrorResponse
            return $violation;
        }
    }

    /**
     * Handles responses to the DELTE request.
     *
     * Dummy method to satisfy interface. Nothing to do, if the DELETE request
     * succeeded or failed.
     * 
     * @param ezcWebdavResponse $response 
     * @return null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
    }
}

?>
