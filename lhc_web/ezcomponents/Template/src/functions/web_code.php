<?php
/**
 * File containing the ezcTemplateWeb class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class contains a bundle of static functions, each implementing a specific
 * function used inside the template language. 
 * 
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateWeb
{
    /**
     * Returns a string that contains the url build of the data $data.
     *
     * @param (array(string=>string) $data 
     * @return string
     */
    public static function url_build( $data )
    {
        $url = '';
        if ( $data['scheme'] && $data['host'] )
        {
            $url .= $data['scheme'] . '://';
            if ( isset( $data['user'] ) )
            {
                $url .= $data['user'];
                if ( isset( $data['pass'] ) )
                {
                    $url .= ':' . $data['pass'];
                };
                $url .= '@';
            }
            $url .= $data['host'];
            if ( isset( $data['port'] ) )
            {
                $url .= ':' . $data['port'];
            }
        }
        $url .= $data['path'];
        if ( isset( $data['query'] ) )
        {
            $url .= '?' . $data['query'];
        }
        if ( isset( $data['fragment'] ) ) 
        {
            $url .= '#' . $data['fragment'];
        }

        return $url;
    }
}

?>
