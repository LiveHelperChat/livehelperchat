<?php
/**
 * File containing the ezcMvcHttpRawRequest class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Class that encapsulates a semi-parsed HTTP request by using PHP's super
 * globals to extract information.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcHttpRawRequest extends ezcBaseStruct
{
    /**
     * Contains an array of headers, where the key is the original HTTP
     * header name, and the value extracted from the $_SERVER superglobal.
     *
     * @var array(string
     */
    public $headers;

    /**
     * Contains the request body (read from php://stdin if available).
     *
     * @var string
     */
    public $body;

    /**
     * Constructs a new ezcMvcHttpRawRequest.
     *
     * @param array(string $headers
     * @param string $body
     */
    public function __construct( $headers = null, $body = '' )
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMvcHttpRawRequest
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcHttpRawRequest( $array['headers'], $array['body'] );
    }
}
?>
