<?php
/**
 * File containing the ezcMvcResult class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This struct contains the result data to be formatted by a response writer.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcResult extends ezcBaseStruct
{
    /**
     * Result status
     *
     * Set this to an object that implements the ezcMvcResultStatusObject, for
     * example ezcMvcResultUnauthorized or ezcMvcExternalRedirect. These status
     * objects are used by the response writers to take appropriate actions.
     *
     * @var ezcMvcResultStatusObject
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
     * Result variables
     *
     * @var array(mixed)
     */
    public $variables;

    /**
     * Constructs a new ezcMvcResult.
     *
     * @param int $status
     * @param DateTime $date
     * @param string $generator
     * @param ezcMvcResultCache $cache
     * @param array(ezcMvcResultCookie) $cookies
     * @param ezcMvcResultContent $content
     * @param array(mixed) $variables
     */
    public function __construct( $status = 0, $date = null,
        $generator = '', $cache = null, $cookies = array(), $content = null,
        $variables = array() )
    {
        $this->status = $status;
        $this->date = $date;
        $this->generator = $generator;
        $this->cache = $cache;
        $this->cookies = $cookies;
        $this->content = $content;
        $this->variables = $variables;
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
     * @return ezcMvcResult
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcResult( $array['status'], $array['date'],
            $array['generator'], $array['cache'], $array['cookies'],
            $array['content'], $array['variables'] );
    }
}
?>
