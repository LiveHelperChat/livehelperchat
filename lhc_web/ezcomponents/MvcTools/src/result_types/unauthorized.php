<?php
/**
 * File containing the ezcMvcResultUnauthorized class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This result type is used to signal a HTTP basic auth header
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcResultUnauthorized implements ezcMvcResultStatusObject
{
    /**
     * The realm is the unique ID to identify a login area
     *
     * @var string
     */
    public $realm;

    /**
     * Constructs an ezcMvcResultUnauthorized object for $realm
     *
     * @param string $realm
     */
    public function __construct( $realm )
    {
        $this->realm = $realm;
    }

    /**
     * Uses the passed in $writer to set the HTTP authentication header.
     *
     * @param ezcMvcResponseWriter $writer
     */
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers['WWW-Authenticate'] = "Basic realm=\"{$this->realm}\"";
        }
    }
}
?>
