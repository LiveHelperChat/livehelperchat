<?php
/**
 * File containing the ezcMvcGzipResponseFilter class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Response filter that gzip's the contents.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcGzipResponseFilter implements ezcMvcResponseFilter
{
    /**
     * This function filters the $response by gzip-encoding it.
     *
     * @param ezcMvcResponse $response
     */
    public function filterResponse( ezcMvcResponse $response )
    {
        $response->body = gzencode( $response->body );
        if ( !$response->content )
        {
            $response->content = new ezcMvcResultContent;
        }
        $response->content->encoding = 'gzip';
    }

    /**
     * Should not be called with any options, as this filter doesn't support any.
     *
     * @throws ezcMvcFilterHasNoOptionsException if the $options array is not
     * empty.
     * @param array $options
     */
    public function setOptions( array $options )
    {
        if ( count( $options ) )
        {
            throw new ezcMvcFilterHasNoOptionsException( __CLASS__ );
        }
    }
}
?>
