<?php
/**
 * File containing the ezcAuthenticationOpenidConnectionException class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Thrown when a host cannot be reached in the OpenID authentication.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationOpenidConnectionException extends ezcAuthenticationOpenidException
{
    /**
     * Constructs a new ezcAuthenticationOpenidConnectionException for the
     * URL $url.
     *
     * @param string $url URL which failed to connect
     * @param string $type An "Accept" header type, like "application/xrds+xml"
     */
    public function __construct( $url, $type = null )
    {
        $message = "Could not connect to {$url}";
        if ( $type !== null )
        {
            $message = $message . ". Type '{$type}' not supported.";
        }

        parent::__construct( $message );
    }
}
?>
