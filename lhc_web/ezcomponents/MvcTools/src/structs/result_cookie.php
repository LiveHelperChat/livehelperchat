<?php
/**
 * File containing the ezcMvcResultCookie class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This struct contains cookie arguments
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcResultCookie extends ezcBaseStruct
{
    /**
     * Cookie name
     *
     * @var string
     */
    public $name;

    /**
     * Cookie value
     *
     * @var string
     */
    public $value;

    /**
     * Expiry date
     *
     * @var DateTime
     */
    public $expire;

    /**
     * Cookie path
     *
     * @var string
     */
    public $path;

    /**
     * Cookie domain
     *
     * @var string
     */
    public $domain;

    /**
     * Whether it is a "secure" cookie
     *
     * @var boolean
     */
    public $secure;

    /**
     * Whether it is a "HTTP-only" cookie
     *
     * @var boolean
     */
    public $httpOnly;

    /**
     * Constructs a new ezcMvcResultCache.
     *
     * @param string $name
     * @param string $value
     * @param DateTime $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct( $name = '', $value = '',
        DateTime $expire = null, $path = '', $domain = '', $secure = false,
        $httpOnly = false )
    {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
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
     * @return ezcMvcResultCache
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcResultCookie( $array['name'], $array['value'],
            $array['expire'], $array['path'], $array['domain'],
            $array['secure'], $array['httpOnly'] );
    }
}
?>
