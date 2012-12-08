<?php
/**
 * File containing the ezcMvcResultCache class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This struct contains the cache control sessints for the result.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcResultCache extends ezcBaseStruct
{
    /**
     * Vary headers for cache control
     *
     * @var string
     */
    public $vary;

    /**
     * Expiry date
     *
     * @var DateTime
     */
    public $expire;

    /**
     * Cache control parameters
     *
     * @var array(string)
     */
    public $controls;

    /**
     * Contains cache pragma settings
     *
     * @var string
     */
    public $pragma;

    /**
     * Last modified date
     *
     * @var DateTime
     */
    public $lastModified;

    /**
     * Constructs a new ezcMvcResultCache.
     *
     * @param string $vary
     * @param DateTime $expire
     * @param array(string) $controls
     * @param string $pragma
     * @param DateTime $lastModified
     */
    public function __construct( $vary = '', $expire = null,
        $controls = null, $pragma = '', $lastModified = null )
    {
        $this->vary = $vary;
        $this->expire = $expire;
        $this->controls = $controls;
        $this->pragma = $pragma;
        $this->lastModified = $lastModified;
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
        return new ezcMvcResultCache( $array['vary'], $array['expire'],
            $array['controls'], $array['pragma'], $array['lastModified'] );
    }
}
?>
