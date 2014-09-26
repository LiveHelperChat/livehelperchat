<?php
/**
 * File containing the ezcAuthenticationOpenidRedirectException class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Thrown when the client could not be redirected in the OpenID authentication.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationOpenidRedirectException extends ezcAuthenticationOpenidException
{
    /**
     * Constructs a new ezcAuthenticationOpenidRedirectException concerning $url.
     *
     * @param string $url The URL where the client could not be redirected
     */
    public function __construct( $url )
    {
        parent::__construct( "Could not redirect to '{$url}'. Most probably your browser does not support redirection or JavaScript." );
    }
}
?>
