<?php
/**
 * File containing the ezcUrlInvalidParameterException class
 *
 * @package Url
 * @version 1.2.2
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcUrlInvalidParameterException is thrown at get/set of a parameter
 * undefined in the configuration.
 *
 * @package Url
 * @version 1.2.2
 */
class ezcUrlInvalidParameterException extends ezcUrlException
{
    /**
     * Constructs a new ezcInvalidParameterException.
     *
     * @param string $param
     */
    public function __construct( $param )
    {
        $message = "The parameter '{$param}' could not be set/get because it is not defined in the configuration.";
        parent::__construct( $message );
    }
}
?>
