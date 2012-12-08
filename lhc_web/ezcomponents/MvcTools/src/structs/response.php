<?php
/**
 * File containing the ezcMvcResponse class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Struct which holds the abstract response.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcResponse extends ezcBaseStruct
{
    /**
     * Result status, which contains additional information about the result, such
     * as a location header (for external redirects), or a www-authenticate information
     * struct.
     *
     * @var ezcBaseStruct
     */
    public $status;

    /**
     * Date of the result
     *
     * @var DateTime
     */
    public $date;

    /**
     * Generator string, f.e. "eZ Components MvcTools"
     *
     * @var string
     */
    public $generator;

    /**
     * Contains cache control settings
     *
     * @var ezcMvcResultCache
     */
    public $cache;

    /**
     * Contains all the cookies to be set
     *
     * @var array(ezcMvcResultCookie)
     */
    public $cookies;

    /**
     * Contains content meta-data, such as language, type, charset.
     *
     * @var ezcMvcResultContent
     */
    public $content;

    /**
     * Server body.
     *
     * @var string
     */
    public $body;

    /**
     * Constructs a new ezcMvcResponse.
     *
     * @param ezcBaseStruct $status
     * @param DateTime $date
     * @param string $generator
     * @param ezcMvcResultCache $cache
     * @param array(ezcMvcResultCookie) $cookies
     * @param ezcMvcResultContent $content
     * @param string $body
     */
    public function __construct( $status = null, $date = null,
        $generator = '', $cache = null, $cookies = array(), $content = null, $body = '' )
    {
        $this->status = $status;
        $this->date = $date;
        $this->generator = $generator;
        $this->cache = $cache;
        $this->cookies = $cookies;
        $this->content = $content;
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
     * @return ezcMvcResponse
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcResponse( $array['status'], $array['date'],
            $array['generator'], $array['cache'], $array['cookies'],
            $array['content'], $array['body'] );
    }
}
?>
