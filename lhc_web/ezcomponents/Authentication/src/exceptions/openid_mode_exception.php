<?php
/**
 * File containing the ezcAuthenticationOpenidModeNotSupportedException class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Thrown when trying OpenID authentication with a mode which is not supported.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationOpenidModeNotSupportedException extends ezcAuthenticationOpenidException
{
    /**
     * Constructs a new ezcAuthenticationOpenidModeNotSupportedException with
     * OpenID mode $mode.
     *
     * @param string $mode OpenID mode which is not supported
     */
    public function __construct( $mode )
    {
        parent::__construct( "OpenID request not supported: 'openid_mode = {$mode}'." );
    }
}
?>
