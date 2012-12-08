<?php
/**
 * File containing the ezcMvcExternalRedirect class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This result type is used to force an external redirect
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcExternalRedirect implements ezcMvcResultStatusObject
{
    /**
     * The location where to re-direct to.
     *
     * @var string
     */
    public $location;

    /**
     * Constructs an ezcMvcExternalRedirect object to re-direct to $location
     *
     * @param string $location
     */
    public function __construct( $location = '/' )
    {
        $this->location = $location;
    }

    /**
     * Uses the passed in $writer to set the proper location header.
     *
     * @param ezcMvcResponseWriter $writer
     */
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers['Location'] = $this->location;
        }
    }
}
?>
