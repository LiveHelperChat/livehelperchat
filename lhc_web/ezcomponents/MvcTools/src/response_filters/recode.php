<?php
/**
 * File containing the ezcMvcRecodeResponseFilter class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Response filter that converts the encoding of the body.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcRecodeResponseFilter implements ezcMvcResponseFilter
{
    /**
     * Contains the from (internal) encoding
     * @var string
     */
    private $fromEncoding = 'utf-8';

    /**
     * Contains the to (external) encoding
     * @var string
     */
    private $toEncoding = 'utf-8';

    /**
     * This function re-codes the response body from charset $fromEncoding to charset $toEncoding.
     *
     * @param ezcMvcResponse $response
     */
    public function filterResponse( ezcMvcResponse $response )
    {
        $test = @iconv( $this->fromEncoding, $this->fromEncoding, $response->body );
        if ( $test !== $response->body )
        {
            throw new ezcMvcInvalidEncodingException( $response->body, $this->fromEncoding );
        }
        $res = @iconv( $this->fromEncoding, $this->toEncoding . '//IGNORE', $response->body );
        $response->body = $res;
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
        foreach ( $options as $option => $value )
        {
            switch ( $option )
            {
                case 'fromEncoding':
                case 'toEncoding':
                    $this->$option = $value;
                    break;
                default:
                    throw new ezcBasePropertyNotFoundException( $option );
            }
        }
    }
}
?>
