<?php
/**
 * File containing the ezcWebdavUnlockRequest class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Abstract representation of a UNLOCK request.
 *
 * An instance of this class represents the WebDAV UNLOCK request.
 *
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavUnlockRequest extends ezcWebdavRequest
{
    /**
     * Validates the headers set in this request.
     *
     * This method validates that all required headers are available and that
     * all feasible headers for this request have valid values.
     *
     * @return void
     *
     * @throws ezcWebdavMissingHeaderException
     *         if a required header is missing.
     * @throws ezcWebdavInvalidHeaderException
     *         if a header is present, but its content does not validate.
     */
    public function validateHeaders()
    {
        if ( !isset( $this->headers['Lock-Token'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'Lock-Token' );
        }

        // Validate common HTTP/WebDAV headers
        parent::validateHeaders();
    }
}

?>
