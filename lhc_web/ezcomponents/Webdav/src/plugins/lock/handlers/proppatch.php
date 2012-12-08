<?php
/**
 * File containing the ezcWebdavLockPropPatchRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the PROPPATCH request.
 *
 * This class provides plugin callbacks for the PROPPATCH request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockPropPatchRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * Handles PROPPATCH requests.
     *
     * Performs all lock related checks necessary for the PROPPATCH request. In
     * case a violation with locks is detected or any other pre-condition check
     * fails, this method returns an instance of {@link ezcWebdavResponse}. If
     * everything is correct, null is returned, so that the $request is handled
     * by the backend.
     *
     * @param ezcWebdavPropPatchRequest $request 
     * @return ezcWebdavResponse
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
                ezcWebdavRequest::DEPTH_ZERO,
                $request->getHeader( 'If' ),
                $request->getHeader( 'Authorization' ),
                ezcWebdavAuthorizer::ACCESS_WRITE,
                $targetLockRefresher,
                false
            ),
            true
        );

        if ( $violation !== null )
        {
            // ezcWebdavErrorResponse
            return $violation;
        }

        // Lock refresh must occur no matter if the request succeeds
        if ( $targetLockRefresher !== null )
        {
            $targetLockRefresher->sendRequests();
        }

        if ( $request->updates->contains( 'lockdiscovery' ) )
        {
            return new ezcWebdavMultistatusResponse(
                new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_409,
                    $request->requestUri,
                    "Property 'lockdiscovery' is readonly."
                )
            );
        }
        if ( $request->updates->contains( 'lockinfo' ) )
        {
            return new ezcWebdavMultistatusResponse(
                new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_409,
                    $request->requestUri,
                    "Property 'lockinfo' is readonly."
                )
            );
        }
    }

    /**
     * Handles responses to the PROPPATCH request.
     *
     * Dummy method to satisfy interface. Does nothing at all, since no checks
     * are necessary.
     * 
     * @param ezcWebdavResponse $response 
     * @return null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
    }
}

?>
