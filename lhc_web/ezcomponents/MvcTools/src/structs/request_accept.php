<?php
/**
 * File containing the ezcMvcRequestAccept class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Struct which defines client-acceptable contents.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRequestAccept extends ezcBaseStruct
{
    /**
     * Request content types.
     *
     * @var array
     */
    public $types;

    /**
     * Acceptable charsets.
     *
     * @var array
     */
    public $charsets;

    /**
     * Request languages.
     *
     * @var array
     */
    public $languages;

    /**
     * Acceptable encodings.
     *
     * @var array
     */
    public $encodings;

    /**
     * Constructs a new ezcMvcRequestAccept.
     *
     * @param array $types
     * @param array $charsets
     * @param array $languages
     * @param array $encodings
     */
    public function __construct( $types = array(),
        $charsets = array(), $languages = array(), $encodings = array() )
    {
        $this->types = $types;
        $this->charsets = $charsets;
        $this->languages = $languages;
        $this->encodings = $encodings;
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
     * @return ezcMvcRequestAccept
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcRequestAccept( $array['types'], $array['charsets'],
            $array['languages'], $array['encodings'] );
    }
}
?>
