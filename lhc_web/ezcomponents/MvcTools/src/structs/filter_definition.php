<?php
/**
 * File containing the ezcMvcFilterDefinition class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This class implements a filter definition to wrap around the filtername and its
 * options.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcFilterDefinition extends ezcBaseStruct
{
    /**
     * Contains the class name of the filter.
     * @var string
     */
    public $className;

    /**
     * Contains an array of filter-specific options.
     * @var array
     */
    public $options;

    /**
     * Constructs a new ezcMvcFilterDefinition.
     *
     * @param string $className
     * @param array $options
     */
    public function __construct( $className = '', $options = array() )
    {
        $this->className = $className;
        $this->options = $options;
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
     * @return ezcMvcFilterDefinition
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcFilterDefinition( $array['className'],
            $array['options'] );
    }
}
?>
