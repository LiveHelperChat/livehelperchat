<?php
/**
 * File containing the ezcMvcInternalRedirect class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The internal redirect object holds a new request object for the next
 * iteration in the dispatcher.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcInternalRedirect extends ezcBaseStruct
{
    /**
     * The new request object
     *
     * @var ezcMvcRequest
     */
    public $request;

    /**
     * Constructs a new ezcMvcInternalRedirect
     *
     * @param ezcMvcRequest $request
     */
    public function __construct( $request = null )
    {
        $this->request = $request;
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
     * @return ezcMvcRequest
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcInternalRedirect( $array['request'] );
    }
}
?>
